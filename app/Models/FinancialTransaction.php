<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_reference',
        'plot_sale_id',
        'debit_account_id',
        'credit_account_id',
        'amount',
         'transaction_date',
         'description',
         'transaction_type'
    ];
}
