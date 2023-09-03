<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('debt_records', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->float("amount_paid");
            $table->float("balance");

            $table->foreignId('bill_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('debtor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_mode_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debt_records');
    }
};
