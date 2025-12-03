<?php

namespace App\Http\Controllers;

use App\Models\InitialBalance;
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
}
