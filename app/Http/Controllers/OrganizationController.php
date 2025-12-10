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
        $getOrganizations = Organization::query()->with(['branches.dailyBalances' => function ($q) {
            $q->latest()->limit(1);
        }])->with(['branches' => function ($q) {
            $q->withCount('transactions');
        }])->get();

        $getOrganizations->each(function ($organization) {
            if ($organization->branches->count() < 1) {
                $organization->closing_balances_total = 0;
                $organization->total_transactions = 0;
            }

            $organization->branches->each(function ($branch) use ($organization) {
                if ($branch->dailyBalances->count() < 1) {
                    $organization->closing_balances_total = 0;
                }

                $branch->dailyBalances->each(function ($dailyBalance) use ($organization) {
                    $organization->closing_balances_total += $dailyBalance->closing_balance ?? 0;
                    $dailyBalance->is_latest_daily_balance = true;
                });

                $organization->total_transactions += $branch->transactions_count ?? 0;
            });
        });

        return ResponseController::success('success get organizations', $getOrganizations);
    }
}
