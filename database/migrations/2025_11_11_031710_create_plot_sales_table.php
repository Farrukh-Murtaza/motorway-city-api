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
        Schema::create('plot_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plot_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('nominee_id')->nullable()->constrained('people')->onDelete('set null');
           $table->foreignId('nominee_relation_id')->nullable()->constrained('nominee_relations')->onDelete('set null');
            $table->timestamp('booking_date')->useCurrent();
            
            // for keeping the registration and application form data
            $table->string('registration_no')->unique();
            $table->string('application_form_no')->unique();
            
            // installment
            $table->decimal('installment_amount', 15, 2);
            $table->integer('total_installments');
            $table->enum('installment_period', ['monthly', 'quarterly', 'six_month', 'yearly']);
            $table->enum('booking_status', ['active', 'completed', 'cancelled', 'defaulted'])->default('active');
            $table->integer('paid_installments')->default(0);
            $table->date('next_installment_date');
            $table->enum('payment_type', ['Installments', 'Full Payment'])->default('Installments');
            $table->enum('payment_method', ['Cash', 'Cheque', 'Bank Transfer']);

            // 
            $table->decimal('marla_rate', 15, 2);
            $table->decimal('down_payment', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('corner_charges', 15, 2)->default(0);
            $table->decimal('park_facing_charges', 15, 2)->default(0);

            $table->timestamp('completion_date')->nullable();
            $table->timestamp('cancellation_date')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();

            $table->index(['booking_status', 'next_installment_date']);
            $table->unique(['plot_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        Schema::table('plot_sales', function (Blueprint $table) {
            $table->dropForeign(['plot_id']);
            $table->dropForeign(['person_id']);
            $table->dropForeign(['nominee_id']);
            $table->dropForeign(['nominee_relation_id']);
        });
        
        // Then drop the tables
        Schema::dropIfExists('plot_sales');
        Schema::dropIfExists('plots');
   
    }
};
