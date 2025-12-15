<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseTrait;
use App\Models\Installment;
use App\Models\InstallmentPlan;
use App\Models\PlotSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstallmentController extends Controller
{

    use ApiResponseTrait;

   public function getPlotSaleInstallments($plotSaleId)
    {
        try {
            $installments = Installment::where('plot_sale_id', $plotSaleId)
                ->orderBy('installment_number')
                ->get();

            return $this->success($installments, 'Installments retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching installments: ' . $e->getMessage());
            return $this->error('Failed to fetch installments', 500);
        }
    }

    /**
     * Get pending installments only
     */
    public function getPendingInstallments($plotSaleId)
    {
        try {
            $installments = Installment::where('plot_sale_id', $plotSaleId)
                ->whereIn('status', ['pending', 'overdue'])
                ->orderBy('installment_number')
                ->get();

            return $this->success($installments, 'Pending installments retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching pending installments: ' . $e->getMessage());
            return $this->error('Failed to fetch pending installments', 500);
        }
    }

    /**
     * Get overdue installments
     */
    public function getOverdueInstallments()
    {
        try {
            $installments = Installment::with(['plotSale.customer', 'plotSale.plot'])
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->orderBy('due_date')
                ->get();

            return $this->success($installments, 'Overdue installments retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching overdue installments: ' . $e->getMessage());
            return $this->error('Failed to fetch overdue installments', 500);
        }
    }

    /**
     * Mark overdue installments (Scheduled job)
     */
    public function markOverdueInstallments()
    {
        try {
            $updated = Installment::where('status', 'pending')
                ->where('due_date', '<', now())
                ->update(['status' => 'overdue']);

            Log::info("Marked {$updated} installments as overdue");

            return $this->success([
                'updated_count' => $updated
            ], 'Overdue installments marked successfully');
        } catch (\Exception $e) {
            Log::error('Error marking overdue installments: ' . $e->getMessage());
            return $this->error('Failed to mark overdue installments', 500);
        }
    }

    /**
     * Calculate late fee for an installment
     */
    public function calculateLateFee($installmentId)
    {
        try {
            $installment = Installment::findOrFail($installmentId);

            if ($installment->status === 'paid') {
                return $this->error('Installment is already paid', 400);
            }

            $daysOverdue = now()->diffInDays($installment->due_date, false);

            if ($daysOverdue <= 0) {
                return $this->success([
                    'late_fee' => 0,
                    'days_overdue' => 0,
                ], 'No late fee applicable');
            }

            // Calculate late fee: 1% per month overdue
            $monthsOverdue = ceil($daysOverdue / 30);
            $lateFeePercentage = 0.01; // 1% per month
            $lateFee = $installment->amount * $lateFeePercentage * $monthsOverdue;

            // Update installment with late fee
            $installment->update([
                'late_fee' => $lateFee,
                'status' => 'overdue',
            ]);

            return $this->success([
                'late_fee' => $lateFee,
                'days_overdue' => $daysOverdue,
                'months_overdue' => $monthsOverdue,
            ], 'Late fee calculated successfully');
        } catch (\Exception $e) {
            Log::error('Error calculating late fee: ' . $e->getMessage());
            return $this->error('Failed to calculate late fee', 500);
        }
    }

    /**
     * Get installment statistics
     */
    public function getStatistics($plotSaleId)
    {
        try {
            $plotSale = PlotSale::findOrFail($plotSaleId);

            $installments = Installment::where('plot_sale_id', $plotSaleId)->get();

            $stats = [
                'total_installments' => $installments->count(),
                'paid_installments' => $installments->where('status', 'paid')->count(),
                'pending_installments' => $installments->where('status', 'pending')->count(),
                'overdue_installments' => $installments->where('status', 'overdue')->count(),
                'total_amount' => $installments->sum('amount'),
                'paid_amount' => $installments->where('status', 'paid')->sum('amount'),
                'pending_amount' => $installments->whereIn('status', ['pending', 'overdue'])->sum('amount'),
                'total_late_fees' => $installments->sum('late_fee'),
                'next_installment' => $installments->where('status', 'pending')->sortBy('installment_number')->first(),
                'completion_percentage' => $plotSale->completion_percentage,
            ];

            return $this->success($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching statistics: ' . $e->getMessage());
            return $this->error('Failed to fetch statistics', 500);
        }
    }

    /**
     * Get upcoming installments (next 30 days)
     */
    public function getUpcomingInstallments()
    {
        try {
            $installments = Installment::with(['plotSale.customer', 'plotSale.plot'])
                ->where('status', 'pending')
                ->whereBetween('due_date', [now(), now()->addDays(30)])
                ->orderBy('due_date')
                ->get();

            return $this->success($installments, 'Upcoming installments retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error fetching upcoming installments: ' . $e->getMessage());
            return $this->error('Failed to fetch upcoming installments', 500);
        }
    }
}
