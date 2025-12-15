<?php namespace App\Services;

use App\Models\Payment;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\PaymentDistribution;
use Illuminate\Support\Facades\DB;

class FlexiblePaymentService
{
    public function processFlexiblePayment($invoiceId, $paymentAmount, $paymentData = [])
    {
        return DB::transaction(function () use ($invoiceId, $paymentAmount, $paymentData) {
            // Get the first pending installment to extract plot_sale_id and plot_id
            $firstInstallment = InstallmentPlan::where('invoice_id', $invoiceId)
                ->pending()
                ->orderBy('due_date')
                ->orderBy('installment_number')
                ->first();

            if (!$firstInstallment) {
                throw new \Exception('No pending installments found for this invoice.');
            }

            // Create the payment record
            $payment = Payment::create([
                'invoice_id' => $invoiceId,
                'plot_sale_id' => $firstInstallment->plot_sale_id,
                'plot_id' => $firstInstallment->plot_id,
                'payment_type' => 'flexible',
                'amount' => $paymentAmount,
                'payment_method' => $paymentData['payment_method'] ?? null,
                'payment_date' => $paymentData['payment_date'] ?? now(),
                'reference_number' => $paymentData['reference_number'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
                'created_by' => auth()->id()
            ]);

            // Get pending installments ordered by due date
            $pendingInstallments = InstallmentPlan::where('invoice_id', $invoiceId)
                ->pending()
                ->orderBy('due_date')
                ->orderBy('installment_number')
                ->get();

            $remainingPayment = $paymentAmount;
            $distributions = [];

            foreach ($pendingInstallments as $installment) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $amountToApply = min($remainingPayment, $installment->remaining_amount);
                
                // Update installment
                $installment->paid_amount += $amountToApply;
                $installment->save();

                // Create distribution record
                $distributions[] = PaymentDistribution::create([
                    'payment_id' => $payment->id,
                    'installment_id' => $installment->id,
                    'amount_applied' => $amountToApply
                ]);

                $remainingPayment -= $amountToApply;
            }

            return [
                'payment' => $payment->load(['plot', 'plotSale']),
                'distributions' => $distributions,
                'remaining_payment' => $remainingPayment
            ];
        });
    }

    public function getPendingInstallments($invoiceId)
    {
        return InstallmentPlan::where('invoice_id', $invoiceId)
            ->with(['plot', 'plotSale'])
            ->pending()
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
                        'plot_number' => $installment->plot->plot_number,
                        'block' => $installment->plot->block ?? null,
                    ],
                    'plot_sale_id' => $installment->plot_sale_id
                ];
            });
    }

    public function getPaymentHistory($invoiceId)
    {
        return Payment::where('invoice_id', $invoiceId)
            ->with(['distributions.installment', 'plot', 'plotSale'])
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date->format('Y-m-d H:i:s'),
                    'payment_method' => $payment->payment_method,
                    'reference_number' => $payment->reference_number,
                    'plot' => [
                        'id' => $payment->plot->id,
                        'plot_number' => $payment->plot->plot_number,
                        'block' => $payment->plot->block ?? null,
                    ],
                    'applied_to' => $payment->distributions->map(function ($dist) {
                        return [
                            'installment_number' => $dist->installment->installment_number,
                            'amount' => $dist->amount_applied
                        ];
                    })
                ];
            });
    }

    public function getPlotInstallmentsSummary($plotId)
    {
        $installments = InstallmentPlan::where('plot_id', $plotId)
            ->with(['plotSale'])
            ->orderBy('due_date')
            ->get();

        return [
            'total_installments' => $installments->count(),
            'total_amount' => $installments->sum('original_amount'),
            'paid_amount' => $installments->sum('paid_amount'),
            'remaining_amount' => $installments->sum('remaining_amount'),
            'pending_count' => $installments->whereIn('status', ['pending', 'partial'])->count(),
            'overdue_count' => $installments->where('status', 'overdue')->count(),
            'installments' => $installments->map(function ($inst) {
                return [
                    'id' => $inst->id,
                    'number' => $inst->installment_number,
                    'due_date' => $inst->due_date->format('Y-m-d'),
                    'original_amount' => $inst->original_amount,
                    'paid_amount' => $inst->paid_amount,
                    'remaining_amount' => $inst->remaining_amount,
                    'status' => $inst->status
                ];
            })
        ];
    }
}