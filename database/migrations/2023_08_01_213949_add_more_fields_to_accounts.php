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
        Schema::table('accounts', function (Blueprint $table) {
            $table->float("expected_mpesa_cash");
            $table->float("actual_cash");
            $table->float("actual_mpesa");
            $table->float("expected_card");
            $table->float("expected_debt");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn("expected_mpesa_cash");
            $table->dropColumn("expected_cash");
            $table->dropColumn("expected_mpesa");
            $table->dropColumn("expected_card");
            $table->dropColumn("expected_debt");
        });
    }
};
