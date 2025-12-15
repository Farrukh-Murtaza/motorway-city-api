<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $accounts = [
            // ASSETS (1000-1999)
            [
                'account_code' => '1000',
                'account_name' => 'Cash',
                'account_type' => 'asset',
                'category' => 'cash',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Cash in hand',
            ],
            [
                'account_code' => '1010',
                'account_name' => 'Bank Account - Main',
                'account_type' => 'asset',
                'category' => 'cash',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Main bank account',
            ],
            [
                'account_code' => '1020',
                'account_name' => 'Bank Account - Operations',
                'account_type' => 'asset',
                'category' => 'cash',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Operational bank account',
            ],
            [
                'account_code' => '1100',
                'account_name' => 'Accounts Receivable',
                'account_type' => 'asset',
                'category' => 'receivable',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Money owed by customers',
            ],
            [
                'account_code' => '1110',
                'account_name' => 'Plot Installments Receivable',
                'account_type' => 'asset',
                'category' => 'receivable',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Outstanding plot installments',
            ],
            [
                'account_code' => '1500',
                'account_name' => 'Plot Inventory',
                'account_type' => 'asset',
                'category' => 'receivable',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Value of available plots',
            ],

            // LIABILITIES (2000-2999)
            [
                'account_code' => '2000',
                'account_name' => 'Accounts Payable',
                'account_type' => 'liability',
                'category' => 'payable',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Money owed to suppliers',
            ],
            [
                'account_code' => '2100',
                'account_name' => 'Customer Advance Payments',
                'account_type' => 'liability',
                'category' => 'payable',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Down payments and advance installments',
            ],
            [
                'account_code' => '2200',
                'account_name' => 'Refunds Payable',
                'account_type' => 'liability',
                'category' => 'payable',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Refunds to be paid to customers',
            ],

            // EQUITY (3000-3999)
            [
                'account_code' => '3000',
                'account_name' => 'Owner\'s Capital',
                'account_type' => 'equity',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Owner investment in business',
            ],
            [
                'account_code' => '3100',
                'account_name' => 'Retained Earnings',
                'account_type' => 'equity',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Accumulated profits',
            ],

            // INCOME/REVENUE (4000-4999)
            [
                'account_code' => '4000',
                'account_name' => 'Plot Sales Revenue',
                'account_type' => 'income',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Revenue from plot sales',
            ],
            [
                'account_code' => '4010',
                'account_name' => 'Corner Plot Charges',
                'account_type' => 'income',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Additional charges for corner plots',
            ],
            [
                'account_code' => '4020',
                'account_name' => 'Park Facing Charges',
                'account_type' => 'income',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Additional charges for park facing plots',
            ],
            [
                'account_code' => '4030',
                'account_name' => 'Late Fee Income',
                'account_type' => 'income',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Late payment fees',
            ],
            [
                'account_code' => '4100',
                'account_name' => 'Transfer Fee Income',
                'account_type' => 'income',
                'category' => 'revenue',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Plot transfer fees',
            ],

            // EXPENSES (5000-5999)
            [
                'account_code' => '5000',
                'account_name' => 'Cost of Land',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Cost of land acquisition',
            ],
            [
                'account_code' => '5100',
                'account_name' => 'Development Expenses',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Land development costs',
            ],
            [
                'account_code' => '5200',
                'account_name' => 'Marketing Expenses',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Advertising and marketing costs',
            ],
            [
                'account_code' => '5300',
                'account_name' => 'Staff Salaries',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Employee salaries and wages',
            ],
            [
                'account_code' => '5400',
                'account_name' => 'Office Rent',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Office space rental',
            ],
            [
                'account_code' => '5410',
                'account_name' => 'Utilities',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Electricity, water, gas, internet',
            ],
            [
                'account_code' => '5500',
                'account_name' => 'Commission Expenses',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Sales agent commissions',
            ],
            [
                'account_code' => '5600',
                'account_name' => 'Legal & Professional Fees',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Legal, accounting, consultancy fees',
            ],
            [
                'account_code' => '5700',
                'account_name' => 'Office Supplies',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Stationery and office supplies',
            ],
            [
                'account_code' => '5800',
                'account_name' => 'Maintenance & Repairs',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Maintenance of property and equipment',
            ],
            [
                'account_code' => '5900',
                'account_name' => 'Miscellaneous Expenses',
                'account_type' => 'expense',
                'category' => 'expense',
                'balance' => 0,
                'is_active' => true,
                'description' => 'Other operating expenses',
            ],
        ];

        foreach ($accounts as $account) {
            DB::table('chart_of_accounts')->insert($account);
        }

        $this->command->info('Chart of Accounts seeded successfully!');
    }
}
