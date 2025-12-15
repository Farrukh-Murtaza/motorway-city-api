<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlotSale extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'person_id',
        'nominee_id',
        'nominee_relation_id',
        'plot_id',
        'booking_date',
        'payment_type',
        'registration_no',
        'application_form_no',
        'installment_amount',
        'total_installments',
        'installment_period',
        'booking_status',
        'paid_installments',
        'next_installment_date',
        'down_payment',
        'total_amount',
        'amount_paid',
        'marla_rate',
        'corner_charges',
        'park_facing_charges',
        'completion_date',
        'cancellation_date',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'next_installment_date' => 'date',
        'completion_date' => 'datetime',
        'cancellation_date' => 'datetime',
        'installment_amount' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'marla_rate' => 'decimal:2',
        'corner_charges' => 'decimal:2',
        'park_facing_charges' => 'decimal:2',
    ];


     public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->amount_paid;
    }

    
    // Relationships

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function customer()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function nominee()
    {
        return $this->belongsTo(Person::class, 'nominee_id');
    }

    public function nomineeRelation()
    {
        return $this->belongsTo(NomineeRelation::class, 'nominee_relation_id');
    }

   
    public function installments()
    {
        return $this->hasMany(Installment::class ,'plot_sale_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function financialTransactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}