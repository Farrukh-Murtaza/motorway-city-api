<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

 protected $fillable = [
        'plot_sale_id',
        'plot_id',
        'payment_reference',
        'payment_type',
        'amount',
        'payment_method',
        'payment_date',
        'transaction_id',
        'notes',
        'attachment',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];


    public function plotSale()
    {
        return $this->belongsTo(PlotSale::class);
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function distributions()
    {
        return $this->hasMany(PaymentDistribution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
