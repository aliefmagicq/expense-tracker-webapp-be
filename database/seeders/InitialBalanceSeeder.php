<?php

namespace Database\Seeders;

use App\Models\InitialBalance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InitialBalance::factory(5)->create();
    }
}
