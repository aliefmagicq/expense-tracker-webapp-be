<?php

namespace App\Http\Controllers;

use App\enum\TransactionType;
use App\Models\Branch;
use App\Models\DailyBalance;
use App\Models\InitialBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class TransactionController extends Controller
{
    public function create (Request $request) 
    {
        $validate = Validator::make($request->all(), [
            'initial_balance_id' => 'required|exists:initial_balances,id',
            'branch_id' => 'required|exists:branches,id',
            'transaction_type' => ['required', new Enum(TransactionType::class)],
            'amount' => 'required|integer',
            'notes' => 'nullable|string|unique:transactions,notes',
            'edit_history' => 'nullable|array'
        ]);

        if ($validate->fails()) {
            return ResponseController::fails(
                'validation error', $validate->errors()->getMessageBag(), null, 422
            );
        }

        /** create transaction */
        $data = $validate->validated();
        $createTransactions = $request->user()->transactions()->create($data);
        
        /** get initial balance amount */
        $initBalance = InitialBalance::query()->where('branch_id', $request->branch_id)->first();
        $initBalanceAmount = $initBalance['amount'];

        /** get closing balance from daily balance */
        $currentBalance = null;
        $ltsDailyBalanceBranch = DailyBalance::query()->where('branch_id', $data['branch_id'])->latest()->first();
        if ($ltsDailyBalanceBranch) {
            $currentBalance = $ltsDailyBalanceBranch['closing_balance'];
        } else {
            $currentBalance = $initBalanceAmount;
        }

        /** insert to initial balance */
        $openingBalance = null;
        $closingBalance = $currentBalance + ($data['transaction_type'] == 'expense' ? -$data['amount'] : +$data['amount']);

        $now = Carbon::now()->format('Y-m-d');
        if ($ltsDailyBalanceBranch && (Carbon::parse($ltsDailyBalanceBranch['updated_at'])->format('Y-m-d') < $now)) {
            $openingBalance = $ltsDailyBalanceBranch['closing_balance'];
        } else {
            $currOpeningBalance = DailyBalance::query()->whereDate('created_at', '=', Carbon::now()->format('Y-m-d'))->first();
            $openingBalance = ($currOpeningBalance['opening_balance']) ?? 0;
        }

        $createDailyBalance = DailyBalance::query()->create([
            'transaction_id' => $createTransactions['id'],
            'branch_id' => $data['branch_id'],
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
        ]);

        return ResponseController::success(
            'create transaction successful', [
                'new_transaction' => $createTransactions,
                'new_daily_balance' => $createDailyBalance
            ] ,201
        );
    }
}
