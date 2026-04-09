<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use App\Models\Branch;
use App\Models\InitialBalance;
use App\Models\Transaction;
use App\Models\DailyBalance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $organizations = Organization::factory(3)
            ->for($user, 'author')
            ->has(Branch::factory(2)->for($user, 'user'), 'branches')
            ->create();

        foreach ($organizations as $organization) {
            foreach ($organization->branches as $branch) {
                InitialBalance::factory(1)->for($branch)->create();
                Transaction::factory(5)->for($user)->for($branch)->create();
                DailyBalance::factory(3)->for($branch)->create();
            }
        }
    }
}
