<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\User;
use App\Models\InitialBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_type' => $this->faker->randomElement(['income', 'expense']),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'notes' => $this->faker->sentence(),
            'edit_history' => json_encode([]),
            'user_id' => User::factory(),
            'branch_id' => Branch::factory(),
            'initial_balance_id' => InitialBalance::factory(),
        ];
    }
}
