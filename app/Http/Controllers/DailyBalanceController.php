<?php

namespace App\Http\Controllers;

use App\Models\DailyBalance;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DailyBalanceController extends Controller
{
    public function getDailyBalances(Request $request)
    {
        $dailyBalances = DailyBalance::all();
        return ResponseController::success('get daily-balances successful', $dailyBalances, 200);
    }

    public function getDailyBalancesPerHours(Request $request)
    {
        /**
         * @var mixed
         * Get all queryParams
         */
        $getDate = $request->get('date');
        $isIncludeTransaction = $request->boolean('include-transaction');

        /**
         * @var mixed
         * Create hours array from 00:00 to 23:59
         */
        $startHourPeriod = Carbon::parse('00:00');
        $endHourPeriod   = Carbon::parse('23:59');
        $period = CarbonPeriod::create($startHourPeriod, '1 hour', $endHourPeriod);
        $hours = [];

        foreach ($period as $date) {
            array_push($hours, $date->format('H:i'));
        }

        /**
         * @var mixed
         * Get transactions with dailyBalance (left joins)
         */
        $transactions = Transaction::query();

        if ($isIncludeTransaction) {
            $transactions->whereDate('transactions.updated_at', '=', $getDate)
                         ->leftJoin('daily_balances', 'transactions.id', '=', 'daily_balances.transaction_id');
        }

        /**
         * @var mixed
         * Mapping transactions by array_reduce
         */
        $result = array_reduce($hours, function ($arr, $current) use ($transactions) {
            $transactionsResult = array_filter($transactions->get()->toArray(), function ($item) use ($current) {
                $toHours = Carbon::parse($item['updated_at'])->format('H:00');
                return (int)explode(':', $toHours)[0] == (int)explode(':', $current)[0];
            });

            $createObj = [
                'hour' => $current,
                'daily_balances' => $transactionsResult
            ];

            array_push($arr, $createObj);
            return $arr;
        }, []);

        return ResponseController::success('get daily-balances per hours successful', $result,200);
    }

    public function getDailyBalancesPerWeeks(Request $request)
    {
        /**
         * @var mixed
         * Get all queryParams
         */
        $getDate = $request->get('date');
        $isIncludeTransaction = $request->boolean('include-transaction');
        
        /**
         * @var mixed
         * Create hours array from 00:00 to 23:59
        */
        $startOfWeeks = Carbon::parse($getDate)->startOfWeek()->format('Y-m-d H:i:s');
        $endOfWeeks = Carbon::parse($getDate)->endOfWeek()->format('Y-m-d H:i:s');
        $period = CarbonPeriod::create($startOfWeeks, '1 day', $endOfWeeks);
        $weeks = [];

        foreach ($period as $week) {
            array_push($weeks, $week);
        }

        /**
         * @var mixed
         * Get transactions with dailyBalance (left joins)
         */
        $transactions = Transaction::query();

        if ($isIncludeTransaction) {
            $transactions->whereBetween('transactions.updated_at', [$startOfWeeks, $endOfWeeks])
                         ->leftJoin('daily_balances', 'transactions.id', '=', 'daily_balances.transaction_id');
        }

        /**
         * @var mixed
         * Mapping transactions by array_reduce
         */
        $result = array_reduce($weeks, function ($arr, $current) use ($transactions) {
            $transactionsResult = array_filter($transactions->get()->toArray(), function ($item) use ($current) {
                $getTransactionDate = Carbon::parse($item['updated_at'])->format('Y-m-d');
                return $getTransactionDate == Carbon::parse($current)->format('Y-m-d');
            });

            $createObj = [
                'week' => $current,
                'daily_balances' => $transactionsResult
            ];

            array_push($arr, $createObj);
            return $arr;
        }, []);

        return ResponseController::success('get daily-balances per weeks successful', $result,200);
    }
}
