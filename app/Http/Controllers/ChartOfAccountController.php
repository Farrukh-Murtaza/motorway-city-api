<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = ChartOfAccount::query();

        if ($request->has('type')) {
            $query->where('account_type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('active')) {
            $query->where('is_active', $request->active);
        }

        $accounts = $query->orderBy('account_code')->get();

        return response()->json([
            'success' => true,
            'data' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|unique:chart_of_accounts',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,income,expense',
            'category' => 'required|in:cash,receivable,payable,revenue,expense',
            'balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $account = ChartOfAccount::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'data' => $account,
        ], 201);
    }

    public function show($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $account,
        ]);
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'account_name' => 'string|max:255',
            'account_type' => 'in:asset,liability,equity,income,expense',
            'category' => 'in:cash,receivable,payable,revenue,expense',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $account->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully',
            'data' => $account,
        ]);
    }

    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        
        // Check if account has transactions
        if ($account->debitTransactions()->exists() || $account->creditTransactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete account with existing transactions',
            ], 400);
        }

        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }
}
