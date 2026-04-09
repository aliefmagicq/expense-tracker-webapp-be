<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InitialBalance;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InitialBalanceController extends Controller
{
    public function create (Request $request) {
        $validate = Validator::make($request->all(), [
            'branch_id' => 'required|unique:initial_balances,branch_id|exists:branches,id',
            'amount' => 'required|integer',
            'notes' => 'nullable|string'
        ]);

        if ($validate->fails()) 
            return ResponseController::fails(
                'validation error', $validate->errors()->getMessageBag(), null, 422
            );

        $data = $validate->validated();
        $newInitialBalance = InitialBalance::query()->create($data);

        return ResponseController::success(
            'create initial_balance successful', $newInitialBalance, 201
        );
    }
    
    public function getInitialBalances(Request $request)
    {
        $getBranches = Branch::query()->whereIn('organization_id', 
                                                Organization::query()->select(['id', 'author_id'])
                                                                             ->pluck('id'))
                                                                             ->pluck('id');

        $initialBalance = InitialBalance::query()->whereIn('branch_id', $getBranches);
        $initialBalanceAmount = $initialBalance->sum('amount');

        return ResponseController::success(
            'get initial-balances sucessful',
            [
                'initial_balances' => $initialBalance->get(),
                'initial_balances_total' => $initialBalanceAmount
            ],
            200
        );
    }
}
