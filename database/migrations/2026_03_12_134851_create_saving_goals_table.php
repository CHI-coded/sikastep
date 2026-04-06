<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saving_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');  // Changed from 'title' to 'name'
            $table->decimal('target_amount', 10, 2);
            $table->date('deadline');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_goals');
    }
};