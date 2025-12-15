<?php namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Record a financial transaction (Double Entry)
     */
    public function recordTransaction(
        $debitAccountCode,
        $creditAccountCode,
        $amount,
        $description,
        $transactionType = 'payment',
        $personId = null,
        $plotSaleId = null,
        $metadata = []
    ) {
        DB::beginTransaction();
        try {
            $debitAccount = ChartOfAccount::where('account_code', $debitAccountCode)->firstOrFail();
            $creditAccount = ChartOfAccount::where('account_code', $creditAccountCode)->firstOrFail();

            // Generate unique transaction reference
            $transactionRef = 'TXN-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Create transaction record
            $transaction = FinancialTransaction::create([
                'transaction_reference' => $transactionRef,
                'person_id' => $personId,
                'plot_sale_id' => $plotSaleId,
                'debit_account_id' => $debitAccount->id,
                'credit_account_id' => $creditAccount->id,
                'amount' => $amount,
                'transaction_date' => now()->toDateString(),
                'description' => $description,
                'transaction_type' => $transactionType,
                'metadata' => json_encode($metadata),
            ]);

            // Update account balances
            $debitAccount->updateBalance($amount, 'debit');
            $creditAccount->updateBalance($amount, 'credit');

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Record plot sale
     */
    public function recordPlotSale($plotSale, $payment)
    {
        // Debit: Cash/Bank (Asset increases)
        // Credit: Plot Sales Revenue (Income increases)
        return $this->recordTransaction(
            debitAccountCode: $payment->payment_method === 'cash' ? '1000' : '1010',
            creditAccountCode: '4000',
            amount: $payment->amount,
            description: "Plot sale payment for Registration #{$plotSale->registration_no}",
            transactionType: 'payment',
            personId: $plotSale->person_id,
            plotSaleId: $plotSale->id,
            metadata: [
                'payment_id' => $payment->id,
                'payment_method' => $payment->payment_method,
                'payment_type' => $payment->payment_type,
            ]
        );
    }

    /**
     * Record installment payment
     */
    public function recordInstallmentPayment($plotSale, $installment, $payment)
    {
        // Debit: Cash/Bank (Asset increases)
        // Credit: Plot Installments Receivable (Asset decreases)
        return $this->recordTransaction(
            debitAccountCode: $payment->payment_method === 'cash' ? '1000' : '1010',
            creditAccountCode: '1110',
            amount: $payment->amount,
            description: "Installment #{$installment->installment_number} payment for Registration #{$plotSale->registration_no}",
            transactionType: 'payment',
            personId: $plotSale->person_id,
            plotSaleId: $plotSale->id,
            metadata: [
                'payment_id' => $payment->id,
                'installment_id' => $installment->id,
                'installment_number' => $installment->installment_number,
            ]
        );
    }

    /**
     * Record late fee
     */
    public function recordLateFee($plotSale, $installment, $lateFee)
    {
        // Debit: Accounts Receivable (Asset increases)
        // Credit: Late Fee Income (Income increases)
        return $this->recordTransaction(
            debitAccountCode: '1100',
            creditAccountCode: '4030',
            amount: $lateFee,
            description: "Late fee for Installment #{$installment->installment_number}, Registration #{$plotSale->registration_no}",
            transactionType: 'payment',
            personId: $plotSale->person_id,
            plotSaleId: $plotSale->id,
            metadata: [
                'installment_id' => $installment->id,
                'installment_number' => $installment->installment_number,
            ]
        );
    }

    /**
     * Get account balance
     */
    public function getAccountBalance($accountCode)
    {
        $account = ChartOfAccount::where('account_code', $accountCode)->first();
        return $account ? $account->balance : 0;
    }

    /**
     * Get trial balance
     */
    public function getTrialBalance()
    {
        $accounts = ChartOfAccount::active()->get();
        
        $trialBalance = [
            'debits' => 0,
            'credits' => 0,
            'accounts' => [],
        ];

        foreach ($accounts as $account) {
            $debitBalance = 0;
            $creditBalance = 0;

            // Assets and Expenses have debit balances
            if (in_array($account->account_type, ['asset', 'expense'])) {
                $debitBalance = $account->balance;
                $trialBalance['debits'] += $debitBalance;
            } else {
                // Liabilities, Equity, and Income have credit balances
                $creditBalance = $account->balance;
                $trialBalance['credits'] += $creditBalance;
            }

            $trialBalance['accounts'][] = [
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'debit' => $debitBalance,
                'credit' => $creditBalance,
            ];
        }

        return $trialBalance;
    }
}