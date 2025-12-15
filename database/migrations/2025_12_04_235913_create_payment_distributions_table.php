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
        Schema::create('payment_distributions', function (Blueprint $table) {
           $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('installment_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_applied', 10, 2);
            $table->timestamps();
            
            $table->index('payment_id');
            $table->index('installment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_distributions');
    }
};
