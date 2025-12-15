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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_sale_id')->constrained('plot_sales')->onDelete('cascade');
            $table->foreignId('plot_id')->constrained()->onDelete('cascade');
            $table->string('payment_reference')->unique();
            $table->enum('payment_type', ['down payment', 'installment','partial']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50)->nullable();
            $table->dateTime('payment_date');
            $table->string('transaction_id', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['plot_sale_id', 'payment_date']);
            $table->index(['plot_id', 'payment_date']);
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
