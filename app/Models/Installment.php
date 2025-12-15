<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

   protected $fillable = [
        'plot_sale_id',
        'plot_id',
        'installment_number',
        'due_date',
        'original_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_date'
    ];

    protected $casts = [
        'due_date' => 'date',
        'original_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

   protected static function boot()
    {
        parent::boot();

        static::saving(function ($installment) {
            // Calculate remaining amount
            $installment->remaining_amount = $installment->original_amount - $installment->paid_amount;
            
            // Update status
            if ($installment->remaining_amount == 0) {
                $installment->status = 'paid';
                if (!$installment->payment_date) {
                    $installment->payment_date = now();
                }
            } elseif ($installment->paid_amount > 0) {
                $installment->status = 'partial';
            } elseif ($installment->due_date < now()->toDateString() && $installment->remaining_amount > 0) {
                $installment->status = 'overdue';
            } else {
                $installment->status = 'pending';
            }
        });
    }

    public function plotSale()
    {
        return $this->belongsTo(PlotSale::class);
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function paymentDistributions()
    {
        return $this->hasMany(PaymentDistribution::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
    
}
