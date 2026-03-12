<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_request_id');
            $table->decimal('amount', 10, 2);
            $table->date('repayment_date');
            $table->timestamps();

            $table->foreign('loan_request_id')->references('id')->on('loan_requests')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
