<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['income', 'expense']);
            $table->integer('amount');
            $table->string('notes');
            $table->json('edit_history');

            $table->foreignId('initial_balance_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('branch_id')->constrained();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
