<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Payment;
use App\Models\PlotSale;
use App\Models\Installment;
use App\Models\PaymentDistribution;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Record flexible payment (partial, full, advance, custom)
     */
    public function recordFlexiblePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'plot_sale_id' => 'required|exists:plot_sales,id',
                'payment_type' => 'required|in:installment,partial,custom,advance',
                'amount' => 'required|numeric|min:0.01',
                'selected_installments' => 'nullable|string',
                'payment_method' => 'required|in:cash,bank_transfer,cheque,online',
                'transaction_id' => 'nullable|string',
                'payment_date' => 'required|date',
                'notes' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            $plotSale = PlotSale::findOrFail($validated['plot_sale_id']);

            if ($plotSale->booking_status !== 'active') {
                return $this->error('Plot sale is not active', 400);
            }

            // Validate amount doesn't exceed remaining balance
            $remainingBalance = $plotSale->total_amount - $plotSale->amount_paid;
            if ($validated['amount'] > $remainingBalance) {
                return $this->error('Payment amount exceeds remaining balance', 400);
            }

            DB::beginTransaction();

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('payments', 'public');
            }

            // Process payment based on type
            $result = match($validated['payment_type']) {
                'installment' => $this->processInstallmentPayment($plotSale, $validated, $attachmentPath),
                'partial' => $this->processPartialPayment($plotSale, $validated, $attachmentPath),
                'advance' => $this->processAdvancePayment($plotSale, $validated, $attachmentPath),
                'custom' => $this->processCustomPayment($plotSale, $validated, $attachmentPath),
                default => throw new \Exception('Invalid payment type'),
            };

            DB::commit();

            return $this->success($result, 'Payment recorded successfully', 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording flexible payment: ' . $e->getMessage());
            return $this->error('Failed to record payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Process full installment payment (one or more installments)
     */
    private function processInstallmentPayment(PlotSale $plotSale, array $data, $attachmentPath)
    {
        $installmentIds = explode(',', $data['selected_installments'] ?? '');
        $installmentIds = array_filter($installmentIds);

        if (empty($installmentIds)) {
            throw new \Exception('No installments selected');
        }

        $installments = Installment::whereIn('id', $installmentIds)
            ->where('plot_sale_id', $plotSale->id)
            ->where('status', '!=', 'paid')
            ->get();

        if ($installments->isEmpty()) {
            throw new \Exception('No valid installments found to pay');
        }

        $totalAmount = 0;
        $payments = [];
        $distributions = [];

        foreach ($installments as $installment) {
            $installmentTotal = $installment->remaining_amount;
            
            // Create payment record for each installment
            $payment = Payment::create([
                'plot_sale_id' => $plotSale->id,
                'plot_id' => $plotSale->plot_id,
                'payment_reference' => $this->generatePaymentReference($plotSale),
                'amount' => $installmentTotal,
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'payment_date' => $data['payment_date'],
                'payment_type' => 'installment',
                'notes' => $data['notes'] ?? "Payment for Installment #{$installment->installment_number}",
                'attachment' => $attachmentPath,
                'created_by' => auth()->id(),
            ]);

            // Create payment distribution
            PaymentDistribution::create([
                'payment_id' => $payment->id,
                'installment_id' => $installment->id,
                'amount_applied' => $installmentTotal
            ]);

            // Update installment - set paid_amount to original_amount to mark as fully paid
            $installment->paid_amount = $installment->original_amount;
            $installment->save(); // This will trigger the boot() method to set status to 'paid'

            // Update plot sale
            $plotSale->increment('amount_paid', $installmentTotal);
            $plotSale->increment('paid_installments');

            $totalAmount += $installmentTotal;
            $payments[] = $payment;
            $distributions[] = [
                'installment_number' => $installment->installment_number,
                'amount' => $installmentTotal,
                'status' => $installment->fresh()->status
            ];

            // Record financial transaction
            $this->recordFinancialTransaction($plotSale, $payment);
        }

        // Update next installment date
        $this->updateNextInstallmentDate($plotSale);

        // Check if fully paid
        $this->checkFullyPaid($plotSale);

        return [
            'payments' => $payments,
            'distributions' => $distributions,
            'total_amount' => $totalAmount,
            'installments_paid' => count($installments),
            'message' => count($installments) . ' installment(s) paid successfully',
        ];
    }

    /**
     * Process partial payment (less than installment amount)
     */
    private function processPartialPayment(PlotSale $plotSale, array $data, $attachmentPath)
    {
        $amount = $data['amount'];
        
        // Get next pending installment
        $nextInstallment = Installment::where('plot_sale_id', $plotSale->id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->orderBy('installment_number')
            ->first();

        if (!$nextInstallment) {
            throw new \Exception('No pending installments found');
        }

        // Create partial payment record
        $payment = Payment::create([
            'plot_sale_id' => $plotSale->id,
            'plot_id' => $plotSale->plot_id,
            'payment_reference' => $this->generatePaymentReference($plotSale),
            'amount' => $amount,
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'payment_date' => $data['payment_date'],
            'payment_type' => 'partial',
            'notes' => $data['notes'] ?? "Partial payment for Installment #{$nextInstallment->installment_number}",
            'attachment' => $attachmentPath,
            'created_by' => auth()->id(),
        ]);

        // Create payment distribution
        PaymentDistribution::create([
            'payment_id' => $payment->id,
            'installment_id' => $nextInstallment->id,
            'amount_applied' => $amount
        ]);

        // Update installment paid_amount
        $nextInstallment->paid_amount += $amount;
        $nextInstallment->save(); // This will auto-update status to 'partial' or 'paid'

        // Update plot sale amount paid
        $plotSale->increment('amount_paid', $amount);

        // If installment is now fully paid, increment paid_installments
        if ($nextInstallment->fresh()->status === 'paid') {
            $plotSale->increment('paid_installments');
            $this->updateNextInstallmentDate($plotSale);
        }

        // Record financial transaction
        $this->recordFinancialTransaction($plotSale, $payment);

        // Check if fully paid
        $this->checkFullyPaid($plotSale);

        $nextInstallment->refresh();

        return [
            'payment' => $payment,
            'installment' => $nextInstallment,
            'remaining_for_installment' => $nextInstallment->remaining_amount,
            'installment_status' => $nextInstallment->status,
            'message' => 'Partial payment recorded successfully',
        ];
    }

    /**
     * Process advance payment (more than current installment)
     */
    private function processAdvancePayment(PlotSale $plotSale, array $data, $attachmentPath)
    {
        $amount = $data['amount'];
        $remainingAmount = $amount;
        $payments = [];
        $distributions = [];
        $installmentsPaid = 0;
        $installmentsPartial = 0;

        // Get all pending installments in order
        $pendingInstallments = Installment::where('plot_sale_id', $plotSale->id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->orderBy('installment_number')
            ->get();

        if ($pendingInstallments->isEmpty()) {
            throw new \Exception('No pending installments found');
        }

        foreach ($pendingInstallments as $installment) {
            if ($remainingAmount <= 0) break;

            $amountToApply = min($remainingAmount, $installment->remaining_amount);
            $paymentType = ($amountToApply >= $installment->remaining_amount) ? 'installment' : 'partial';

            // Create payment record
            $payment = Payment::create([
                'plot_sale_id' => $plotSale->id,
                'plot_id' => $plotSale->plot_id,
                'payment_reference' => $this->generatePaymentReference($plotSale),
                'amount' => $amountToApply,
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'payment_date' => $data['payment_date'],
                'payment_type' => $paymentType,
                'notes' => $data['notes'] ?? "Advance payment for Installment #{$installment->installment_number}",
                'attachment' => $attachmentPath,
                'created_by' => auth()->id(),
            ]);

            // Create payment distribution
            PaymentDistribution::create([
                'payment_id' => $payment->id,
                'installment_id' => $installment->id,
                'amount_applied' => $amountToApply
            ]);

            // Update installment
            $installment->paid_amount += $amountToApply;
            $installment->save();

            $payments[] = $payment;
            $distributions[] = [
                'installment_number' => $installment->installment_number,
                'amount' => $amountToApply,
                'status' => $installment->fresh()->status
            ];

            if ($installment->fresh()->status === 'paid') {
                $plotSale->increment('paid_installments');
                $installmentsPaid++;
            } elseif ($installment->fresh()->status === 'partial') {
                $installmentsPartial++;
            }

            $remainingAmount -= $amountToApply;

            // Record financial transaction
            $this->recordFinancialTransaction($plotSale, $payment);
        }

        // Update plot sale
        $plotSale->increment('amount_paid', $amount);
        $this->updateNextInstallmentDate($plotSale);

        // Check if fully paid
        $this->checkFullyPaid($plotSale);

        return [
            'payments' => $payments,
            'distributions' => $distributions,
            'total_amount' => $amount,
            'installments_paid' => $installmentsPaid,
            'installments_partial' => $installmentsPartial,
            'message' => "Advance payment processed. {$installmentsPaid} installment(s) paid, {$installmentsPartial} partially paid",
        ];
    }

    /**
     * Process custom amount payment
     */
    private function processCustomPayment(PlotSale $plotSale, array $data, $attachmentPath)
    {
        // Custom payment is treated as advance payment
        return $this->processAdvancePayment($plotSale, $data, $attachmentPath);
    }

    /**
     * Get pending installments for a plot sale
     */
    public function getPendingInstallments($plotSaleId)
    {
        try {
            $installments = Installment::where('plot_sale_id', $plotSaleId)
                ->with(['plot'])
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->orderBy('due_date')
                ->orderBy('installment_number')
                ->get()
                ->map(function ($installment) {
                    return [
                        'id' => $installment->id,
                        'installment_number' => $installment->installment_number,
                        'due_date' => $installment->due_date->format('Y-m-d'),
                        'original_amount' => $installment->original_amount,
                        'paid_amount' => $installment->paid_amount,
                        'remaining_amount' => $installment->remaining_amount,
                        'status' => $installment->status,
                        'is_overdue' => $installment->status === 'overdue',
                        'plot' => [
                            'id' => $installment->plot->id,
                            'name' => $installment->plot->name,
                        ]
                    ];
                });

            return $this->success($installments);

        } catch (\Exception $e) {
            Log::error('Error fetching pending installments: ' . $e->getMessage());
            return $this->error('Failed to fetch installments', 500);
        }
    }

    /**
     * Get payment history for a plot sale
     */
    public function getPaymentHistory($plotSaleId)
    {
        try {
            $payments = Payment::where('plot_sale_id', $plotSaleId)
                ->with(['distributions.installment', 'plot', 'creator'])
                ->orderBy('payment_date', 'desc')
                ->get()
                ->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'payment_reference' => $payment->payment_reference,
                        'amount' => $payment->amount,
                        'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
                        'payment_method' => $payment->payment_method,
                        'payment_type' => $payment->payment_type,
                        'transaction_id' => $payment->transaction_id,
                        'notes' => $payment->notes,
                        'attachment' => $payment->attachment,
                        'attachment_url' => $payment->attachment ? asset('storage/' . $payment->attachment) : null,
                        'created_by' => $payment->creator ? $payment->creator->name : null,
                        'plot' => [
                            'id' => $payment->plot->id,
                            'name' => $payment->plot->name,
                        ],
                        'applied_to_installments' => $payment->distributions->map(function ($dist) {
                            return [
                                'installment_number' => $dist->installment->installment_number,
                                'amount_applied' => $dist->amount_applied,
                                'installment_status' => $dist->installment->status
                            ];
                        })
                    ];
                });

            return $this->success($payments);

        } catch (\Exception $e) {
            Log::error('Error fetching payment history: ' . $e->getMessage());
            return $this->error('Failed to fetch payment history', 500);
        }
    }

    /**
     * Get installment summary for a plot sale
     */
    public function getPlotSaleInstallmentsSummary($plotSaleId)
    {
        try {
            $installments = Installment::where('plot_sale_id', $plotSaleId)
                ->orderBy('installment_number')
                ->get();

            $summary = [
                'total_installments' => $installments->count(),
                'total_amount' => $installments->sum('original_amount'),
                'paid_amount' => $installments->sum('paid_amount'),
                'remaining_amount' => $installments->sum('remaining_amount'),
                'pending_count' => $installments->whereIn('status', ['pending', 'partial'])->count(),
                'overdue_count' => $installments->where('status', 'overdue')->count(),
                'paid_count' => $installments->where('status', 'paid')->count(),
                'installments' => $installments->map(function ($inst) {
                    return [
                        'id' => $inst->id,
                        'number' => $inst->installment_number,
                        'due_date' => $inst->due_date->format('Y-m-d'),
                        'original_amount' => $inst->original_amount,
                        'paid_amount' => $inst->paid_amount,
                        'remaining_amount' => $inst->remaining_amount,
                        'status' => $inst->status,
                        'payment_date' => $inst->payment_date ? $inst->payment_date->format('Y-m-d') : null
                    ];
                })
            ];

            return $this->success($summary);

        } catch (\Exception $e) {
            Log::error('Error fetching installments summary: ' . $e->getMessage());
            return $this->error('Failed to fetch installments', 500);
        }
    }

    /**
     * Update next installment date
     */
    private function updateNextInstallmentDate(PlotSale $plotSale)
    {
        $nextInstallment = Installment::where('plot_sale_id', $plotSale->id)
            ->whereIn('status', ['pending', 'partial', 'overdue'])
            ->orderBy('due_date')
            ->orderBy('installment_number')
            ->first();

        if ($nextInstallment) {
            $plotSale->update([
                'next_installment_date' => $nextInstallment->due_date,
            ]);
        } else {
            $plotSale->update([
                'next_installment_date' => null,
            ]);
        }
    }

    /**
     * Check if plot sale is fully paid
     */
    private function checkFullyPaid(PlotSale $plotSale)
    {
        $plotSale->refresh();

        if ($plotSale->amount_paid >= $plotSale->total_amount) {
            $plotSale->update([
                'booking_status' => 'completed',
                'completion_date' => now(),
            ]);

            $plotSale->plot->update(['status' => 'sold']);
        }
    }

    /**
     * Generate unique payment reference
     */
    private function generatePaymentReference(PlotSale $plotSale)
    {
        $prefix = 'PAY';
        $registration = $plotSale->registration_no;
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(uniqid(), -4));
        
        return "{$prefix}-{$registration}-{$timestamp}-{$random}";
    }

    /**
     * Record financial transaction
     */
    private function recordFinancialTransaction(PlotSale $plotSale, Payment $payment)
    {
        try {
            $debitAccountCode = match($payment->payment_method) {
                'cash' => '1000',
                'bank_transfer', 'cheque', 'online' => '1010',
                default => '1000',
            };

            $creditAccountCode = match($payment->payment_type) {
                'installment', 'partial' => '1110', // Installments Receivable
                'advance' => '4000', // Sales Revenue
                default => '4000',
            };

            $this->accountingService->recordTransaction(
                debitAccountCode: $debitAccountCode,
                creditAccountCode: $creditAccountCode,
                amount: $payment->amount,
                description: $this->getTransactionDescription($payment, $plotSale),
                transactionType: 'payment',
                personId: $plotSale->person_id,
                plotSaleId: $plotSale->id,
                metadata: [
                    'payment_id' => $payment->id,
                    'payment_reference' => $payment->payment_reference,
                    'payment_type' => $payment->payment_type,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error recording financial transaction: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction description
     */
    private function getTransactionDescription(Payment $payment, PlotSale $plotSale)
    {
        $type = ucfirst($payment->payment_type);
        $regNo = $plotSale->registration_no;
        $amount = number_format($payment->amount, 2);
        
        // Get installment numbers from distributions
        $installmentNumbers = $payment->distributions->pluck('installment.installment_number')->join(', ');
        
        if ($installmentNumbers) {
            return "{$type} payment of PKR {$amount} for Installment(s) #{$installmentNumbers}, Plot Sale #{$regNo}";
        }
        
        return "{$type} payment of PKR {$amount} for Plot Sale #{$regNo}";
    }
}