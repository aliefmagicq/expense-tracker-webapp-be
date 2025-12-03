<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyBalance>
 */
class DailyBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $openingBalance = $this->faker->randomFloat(2, 1000, 50000);
        $transactionAmount = $this->faker->randomFloat(2, 100, 5000);

        return [
            'transaction_id' => Transaction::factory(),
            'branch_id' => Branch::factory(),
            'opening_balance' => $openingBalance,
            'closing_balance' => $openingBalance + $transactionAmount,
        ];
    }
}
