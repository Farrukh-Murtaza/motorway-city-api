<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlotSaleResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Person;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Plot;
use App\Models\PlotSale;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PlotSaleController extends Controller
{
    use ApiResponseTrait;

    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = PlotSale::with([
                'plot',
                'customer'
            ]);

            // Apply filters
            if ($request->has('status')) {
                $query->where('booking_status', $request->status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('registration_no', 'like', "%{$search}%")
                      ->orWhere('application_form_no', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('cnic', 'like', "%{$search}%");
                      });
                });
            }

            $plotSales = $query->orderBy('booking_date', 'desc')->get();

            return $this->success(PlotSaleResource::collection($plotSales), 'Plot sales retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching plot sales: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->error('Failed to fetch plot sales', 500);
        }
    }

    /**
     * Load necessary data for creating a plot sale
     */
    public function loadData()
    {
        try {
            $persons = Person::select('id', 'first_name', 'last_name', 'cnic')->get();
            $plots = Plot::select('id', 'plot_number', 'marla', 'block_id', 'status')
                ->where('status', 'available')
                ->get();
            
            $data = [
                'plots' => $plots,
                'persons' => $persons,
            ];
            
            return $this->success($data, 'Data loaded successfully');
        } catch (\Exception $e) {
            Log::error('Error loading data: ' . $e->getMessage());
            return $this->error('Failed to load data', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'plot_id' => 'required|exists:plots,id',
                'person_id' => 'required|exists:people,id',
                'nominee_id' => 'nullable|exists:people,id',
                'nominee_relation_id' => 'nullable|exists:nominee_relations,id',
                'registration_no' => 'required|string|unique:plot_sales,registration_no',
                'application_form_no' => 'required|integer|unique:plot_sales,application_form_no',
                'payment_method' => 'required|in:Cash,Cheque,Bank Transfer',
                'payment_type' => 'required|in:Installments,Full Payment',
                'marla_rate' => 'required|numeric|min:0',
                'corner_charges' => 'nullable|numeric|min:0',
                'park_facing_charges' => 'nullable|numeric|min:0',
                'down_payment' => 'required|numeric|min:0',
                'installment_amount' => 'required_if:payment_type,Installments|nullable|numeric|min:0',
                'total_installments' => 'required_if:payment_type,Installments|nullable|integer|min:1|max:120',
                'installment_period' => 'required_if:payment_type,Installments|nullable|in:monthly,quarterly,six_month,yearly',
                'next_installment_date' => 'required_if:payment_type,Installments|nullable|date|after:today',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check if plot is available
            $plot = Plot::findOrFail($validated['plot_id']);
            if ($plot->status !== 'available') {
                return $this->error('Plot is not available for sale', 400);
            }

            // Calculate total amount
            $totalAmount = ($plot->marla * $validated['marla_rate']) + 
                          ($validated['corner_charges'] ?? 0) + 
                          ($validated['park_facing_charges'] ?? 0);

            // Validate down payment
            if ($validated['down_payment'] > $totalAmount) {
                return $this->error('Down payment cannot exceed total amount', 400);
            }

            // Create plot sale in a transaction
            DB::beginTransaction();

            $plotSale = PlotSale::create([
                'person_id' => $validated['person_id'],
                'nominee_id' => $validated['nominee_id'] ?? null,
                'nominee_relation_id' => $validated['nominee_relation_id'] ?? null,
                'plot_id' => $plot->id,
                'booking_date' => now(),
                'payment_type' => $validated['payment_type'],
                'registration_no' => $validated['registration_no'],
                'application_form_no' => $validated['application_form_no'],
                'marla_rate' => $validated['marla_rate'],
                'corner_charges' => $validated['corner_charges'] ?? 0,
                'park_facing_charges' => $validated['park_facing_charges'] ?? 0,
                'down_payment' => $validated['down_payment'],
                'total_amount' => $totalAmount,
                'amount_paid' => $validated['down_payment'],
                'installment_amount' => $validated['installment_amount'] ?? null,
                'total_installments' => $validated['total_installments'] ?? null,
                'installment_period' => $validated['installment_period'] ?? null,
                'next_installment_date' => $validated['next_installment_date'] ?? null,
                'booking_status' => 'active',
                'paid_installments' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create installments if payment type is installment
            if ($plotSale->payment_type === 'Installments') {
                $this->createInstallments($plotSale);
            }

            // Update plot status
            $plot->update(['status' => 'booked']);

            // Record down payment
            $this->recordDownPayment($plotSale, $validated);

            // Record financial transaction for down payment
            $this->recordFinancialTransaction($plotSale, $validated);

            DB::commit();

            // Load relationships for response
            $plotSale->load(['customer', 'plot', 'installments']);


            return $this->success($plotSale, 'Plot sale created successfully', 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating plot sale: ' . $e->getMessage());
            return $this->error('Failed to create plot sale: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $plotSale = PlotSale::with([
                'customer',
                'nominee',
                'nomineeRelation',
                'plot',
                'invoice.installments' => function($query) {
                    $query->orderBy('installment_number');
                },
                'invoice.payments' => function($query) {
                    $query->orderBy('payment_date', 'desc');
                }
            ])->findOrFail($id);

            $data = [
                'plot_sale' => $plotSale,
                'summary' => [
                    'total_amount' => $plotSale->total_amount,
                    'amount_paid' => $plotSale->amount_paid,
                    'remaining_amount' => $plotSale->total_amount - $plotSale->amount_paid,
                    'paid_installments' => $plotSale->invoice ? $plotSale->invoice->installments->where('status', 'paid')->count() : 0,
                    'total_installments' => $plotSale->total_installments,
                    'remaining_installments' => $plotSale->total_installments - ($plotSale->invoice ? $plotSale->invoice->installments->where('status', 'paid')->count() : 0),
                    'next_installment_date' => $plotSale->next_installment_date,
                    'completion_percentage' => $plotSale->total_amount > 0 ? ($plotSale->amount_paid / $plotSale->total_amount) * 100 : 0,
                ],
            ];

            return $this->success($data, 'Plot sale retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching plot sale: ' . $e->getMessage());
            return $this->error('Plot sale not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $plotSale = PlotSale::findOrFail($id);

            $validated = $request->validate([
                'notes' => 'nullable|string|max:1000',
                'booking_status' => 'nullable|in:active,completed,cancelled,defaulted',
            ]);

            $plotSale->update($validated);

            return $this->success($plotSale, 'Plot sale updated successfully');
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            Log::error('Error updating plot sale: ' . $e->getMessage());
            return $this->error('Failed to update plot sale', 500);
        }
    }

    /**
     * Cancel a plot sale
     */
    public function cancel(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            $plotSale = PlotSale::findOrFail($id);

            if ($plotSale->booking_status === 'cancelled') {
                return $this->error('Plot sale is already cancelled', 400);
            }

            $plotSale->update([
                'booking_status' => 'cancelled',
                'cancellation_date' => now(),
                'cancellation_reason' => $validated['cancellation_reason'],
            ]);

            // Update plot status back to available
            $plotSale->plot->update(['status' => 'available']);

            // Cancel all pending installments
            if ($plotSale->invoice) {
                Installment::where('invoice_id', $plotSale->invoice->id)
                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                    ->update(['status' => 'cancelled']);
            }

            DB::commit();

            return $this->success($plotSale, 'Plot sale cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling plot sale: ' . $e->getMessage());
            return $this->error('Failed to cancel plot sale', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $plotSale = PlotSale::findOrFail($id);

            // Only allow deletion if no payments have been made beyond down payment
            if ($plotSale->amount_paid > $plotSale->down_payment) {
                return $this->error('Cannot delete plot sale with payments', 400);
            }

            // Update plot status
            $plotSale->plot->update(['status' => 'available']);

            $plotSale->delete();

            DB::commit();

            return $this->success(null, 'Plot sale deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting plot sale: ' . $e->getMessage());
            return $this->error('Failed to delete plot sale', 500);
        }
    }

    /**
     * Get ownership history of a plot
     */
    public function getPlotOwnershipHistory($plotId)
    {
        try {
            $history = PlotSale::with(['customer:id,first_name,last_name,cnic'])
                ->where('plot_id', $plotId)
                ->orderBy('booking_date', 'desc')
                ->get()
                ->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'customer' => $sale->customer->first_name . ' ' . $sale->customer->last_name,
                        'cnic' => $sale->customer->cnic,
                        'booking_date' => $sale->booking_date,
                        'status' => $sale->booking_status,
                        'registration_no' => $sale->registration_no,
                        'total_amount' => $sale->total_amount,
                        'amount_paid' => $sale->amount_paid,
                        'cancellation_date' => $sale->cancellation_date,
                        'completion_date' => $sale->completion_date,
                    ];
                });

            return $this->success($history, 'Plot ownership history retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching plot ownership history: ' . $e->getMessage());
            return $this->error('Failed to fetch ownership history', 500);
        }
    }

    /**
     * Get customer purchase history
     */
    public function getCustomerPurchaseHistory($personId)
    {
        try {
            $purchases = PlotSale::with(['plot:id,name,block,street_no'])
                ->where('person_id', $personId)
                ->orderBy('booking_date', 'desc')
                ->get()
                ->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'plot_name' => $sale->plot->name,
                        'plot_block' => $sale->plot->block,
                        'registration_no' => $sale->registration_no,
                        'booking_date' => $sale->booking_date,
                        'status' => $sale->booking_status,
                        'total_amount' => $sale->total_amount,
                        'amount_paid' => $sale->amount_paid,
                        'remaining_amount' => $sale->total_amount - $sale->amount_paid,
                    ];
                });

            return $this->success($purchases, 'Customer purchase history retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching customer purchase history: ' . $e->getMessage());
            return $this->error('Failed to fetch purchase history', 500);
        }
    }

   

    /**
     * Create installments for plot sale
     */
    private function createInstallments(PlotSale $plotSale): void
    {
        $installmentAmount = $plotSale->installment_amount;
        $totalInstallments = $plotSale->total_installments;
        $nextDate = $plotSale->next_installment_date;

        $installments = [];
        for ($i = 1; $i <= $totalInstallments; $i++) {
            $installments[] = [
                'plot_sale_id' => $plotSale->id,
                'plot_id' => $plotSale->plot_id,
                'installment_number' => $i,
                'due_date' => $nextDate,
                'original_amount' => $installmentAmount,
                'paid_amount' => 0,
                'remaining_amount' => $installmentAmount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Calculate next installment date
            $nextDate = $this->calculateNextInstallmentDate($nextDate, $plotSale->installment_period);
        }

        // Bulk insert for better performance
        Installment::insert($installments);
    }

    /**
     * Calculate next installment date based on period
     */
    private function calculateNextInstallmentDate($currentDate, $period)
    {
        $date = \Carbon\Carbon::parse($currentDate);

        return match($period) {
            'monthly' => $date->addMonth(),
            'quarterly' => $date->addMonths(3),
            'six_month' => $date->addMonths(6),
            'yearly' => $date->addYear(),
            default => $date->addMonth(),
        };
    }

    /**
     * Record down payment
     */
    private function recordDownPayment(PlotSale $plotSale, array $validated): void
    {
        try {
            // Create payment record
            $payment = Payment::create([
                'plot_sale_id' => $plotSale->id,
                'plot_id' => $plotSale->plot_id,
                'payment_reference' => 'PAY-' . $plotSale->registration_no . '-' . now()->format('YmdHis'),
                'amount' => $validated['down_payment'],
                'payment_method' => strtolower(str_replace(' ', '_', $validated['payment_method'])),
                'payment_date' => now(),
                'payment_type' => 'down payment',
                'notes' => 'Down payment for plot booking',
                'created_by' => auth()->id()
            ]);

            Log::info('Down payment recorded successfully', ['payment_id' => $payment->id]);
            
        } catch (\Exception $e) {
            Log::error('Error recording down payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Record financial transaction
     */
    private function recordFinancialTransaction(PlotSale $plotSale, array $validated): void
    {
        try {
            // Determine account codes based on payment method
            $debitAccountCode = match($validated['payment_method']) {
                'Cash' => '1000',
                'Bank Transfer' => '1010',
                'Cheque' => '1010',
                default => '1000',
            };

            $metadata = [
                'plot_sale_id' => $plotSale->id,
                'registration_no' => $plotSale->registration_no,
                'payment_method' => $validated['payment_method'],
                'payment_type' => 'down_payment',
            ];

            // Record down payment transaction
            $this->accountingService->recordTransaction(
                debitAccountCode: $debitAccountCode,
                creditAccountCode: '4000', // Plot Sales Revenue
                amount: $validated['down_payment'],
                description: "Down payment for Plot Sale #{$plotSale->registration_no}",
                transactionType: 'payment',
                personId: $plotSale->person_id,
                plotSaleId: $plotSale->id,
                metadata: $metadata
            );

            // Record corner charges if applicable
            if (($validated['corner_charges'] ?? 0) > 0) {
                $this->accountingService->recordTransaction(
                    debitAccountCode: $debitAccountCode,
                    creditAccountCode: '4010', // Corner Plot Charges
                    amount: $validated['corner_charges'],
                    description: "Corner charges for Plot Sale #{$plotSale->registration_no}",
                    transactionType: 'payment',
                    personId: $plotSale->person_id,
                    plotSaleId: $plotSale->id,
                    metadata: $metadata
                );
            }

            // Record park facing charges if applicable
            if (($validated['park_facing_charges'] ?? 0) > 0) {
                $this->accountingService->recordTransaction(
                    debitAccountCode: $debitAccountCode,
                    creditAccountCode: '4020', // Park Facing Charges
                    amount: $validated['park_facing_charges'],
                    description: "Park facing charges for Plot Sale #{$plotSale->registration_no}",
                    transactionType: 'payment',
                    personId: $plotSale->person_id,
                    plotSaleId: $plotSale->id,
                    metadata: $metadata
                );
            }

        } catch (\Exception $e) {
            Log::error('Error recording financial transaction: ' . $e->getMessage());
            throw $e;
        }
    }
    
}