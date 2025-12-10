<?php

namespace App\Http\Controllers;

use App\Models\DailyBalance;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganizationController extends Controller
{
    public function create (Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|unique:organizations,name',
            'description' => 'nullable|string',
        ]);

        if ($validate->fails()) 
            return ResponseController::fails(
                'validation err', $validate->errors()->getMessageBag(), null, 422
            );

        $data = $validate->validated();
        $newOrg = $request->user()->organizations()->create($data);

        return response()->json([
            'new_organization' => $newOrg
        ]);
    }

    public function getOrganizations () {
        $getOrganizations = Organization::query()->with(['branches' => function ($q) {
            $q->with(['dailyBalances' => function ($q) {
                $q->latest()->limit(1);
            }])->withCount('transactions')
                ->withSum(['transactions as transaction_expense_total' => function ($q) {
                    $q->where('transaction_type', 'expense');
                }], 'amount')
                ->withSum(['transactions as transaction_income_total' => function ($q) {
                    $q->where('transaction_type', 'income');
                }], 'amount');
        }])->get();

        $getOrganizations->each(function ($organization, $i) {

            /**
             * Loop thorugh branches
             */
            $branches = $organization->branches; 
            if ($branches->count() < 1) {
                $organization->closing_balances_total = 0;
                $organization->transactions_total = 0;
            }

            $branches->each(function ($branch, $i) use ($organization) {
            
                /**
                 * Daily balances
                 */
                $dailyBalances = $branch->dailyBalances;
                if ($dailyBalances->count() < 1) {
                    $organization->closing_balances_total = 0;
                }

                $dailyBalances->each(function ($dailyBalance, $i) use ($organization) {
                    $closingBalance = $dailyBalance->closing_balance;
                    $organization->closing_balances_total += $closingBalance;
                    $dailyBalance->is_latest_daily_balance = true;
                });

                /**
                 * Transactions
                 */
                $transactionCount = $branch->transactions_count;
                $transactionIncome = $branch->transaction_income_total ?? 0;
                $transactionExpense = $branch->transaction_expense_total ?? 0;

                $organization->transactions_total += (int)$transactionCount;
                $organization->transactions_income_total += (int)$transactionIncome;
                $organization->transactions_expense_total += (int)$transactionExpense;
            });
        });

        return ResponseController::success('success get organizations', $getOrganizations, 200);
    }
}
