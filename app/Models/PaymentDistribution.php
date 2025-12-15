<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'installment_id',
        'amount_applied'
    ];

    protected $casts = [
        'amount_applied' => 'decimal:2'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }
}
