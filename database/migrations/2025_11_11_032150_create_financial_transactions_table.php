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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->foreignId('plot_sale_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('debit_account_id')->constrained('chart_of_accounts');
            $table->foreignId('credit_account_id')->constrained('chart_of_accounts');
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->text('description');
            $table->enum('transaction_type', ['payment', 'refund', 'expense', 'adjustment']);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['transaction_date', 'transaction_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
