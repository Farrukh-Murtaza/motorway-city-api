<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

     protected $fillable = [
        'plot_sale_id',      // Which plot sale this invoice is for
        'plot_id',           // Which plot (for quick reference)
        'person_id',       // Who is buying the plot
        'invoice_number',    // INV-2024-001
        'invoice_date',      // When invoice was created
        'due_date',          // When full payment is due (if paying in full)
        'total_amount',      // Total price of the plot
        'paid_amount',       // How much has been paid so far
        'remaining_amount',  // How much is still owed
        'payment_type',      // 'full', 'installment', 'flexible'
        'status',            // 'unpaid', 'partial', 'paid', 'overdue'
        'notes',             // Any additional info
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
        });

        static::updating(function ($invoice) {
            $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
        });
    }

    // ADD THIS METHOD
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        
        // Get the last invoice for this year
        $lastInvoice = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        // Increment the number
        $number = $lastInvoice ? ((int)substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        // Format: INV-2024-0001
        return sprintf('%s-%s-%04d', $prefix, $year, $number);
    }

    // Relationships
    public function plotSale()
    {
        return $this->belongsTo(PlotSale::class);
    }

    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    public function customer()
    {
        return $this->belongsTo(Person::class, 'customer_id');
    }

    public function installments()
    {
        return $this->hasMany(InstallmentPlan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
}
