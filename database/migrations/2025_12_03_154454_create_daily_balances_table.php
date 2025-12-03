<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_balances', function (Blueprint $table) {
            $table->id();
            $table->integer('opening_balance');
            $table->integer('closing_balance');

            $table->foreignId('transaction_id')->constrained();
            $table->foreignId('branch_id')->constrained();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_balances');
    }
};
