<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'category',
        'balance',
        'is_active',
        'description',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Relationships
    public function debitTransactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'debit_account_id');
    }

    public function creditTransactions()
    {
        return $this->hasMany(FinancialTransaction::class, 'credit_account_id');
    }

    // Helper methods
    public function updateBalance($amount, $type = 'credit')
    {
        if ($type === 'debit') {
            // For assets and expenses, debit increases balance
            if (in_array($this->account_type, ['asset', 'expense'])) {
                $this->increment('balance', $amount);
            } else {
                $this->decrement('balance', $amount);
            }
        } else {
            // For liabilities, equity, and income, credit increases balance
            if (in_array($this->account_type, ['liability', 'equity', 'income'])) {
                $this->increment('balance', $amount);
            } else {
                $this->decrement('balance', $amount);
            }
        }
    }

    public static function getAssetAccounts()
    {
        return self::byType('asset')->active()->get();
    }

    public static function getLiabilityAccounts()
    {
        return self::byType('liability')->active()->get();
    }

    public static function getIncomeAccounts()
    {
        return self::byType('income')->active()->get();
    }

    public static function getExpenseAccounts()
    {
        return self::byType('expense')->active()->get();
    }

    public static function getCashAccounts()
    {
        return self::byCategory('cash')->active()->get();
    }

}
