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
        Schema::create('user_auths', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->boolean('authenticated');

            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_auths');
    }
};
