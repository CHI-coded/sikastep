<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saving_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('saving_goal_id');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->timestamps();

            $table->foreign('saving_goal_id')->references('id')->on('saving_goals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_transactions');
    }
};