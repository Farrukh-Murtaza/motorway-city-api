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

        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('block')->default('');
            $table->string('street_no')->default('');
            
            $table->double('width', 6, 2);   // feet or meters
            $table->double('length', 6, 2);
            
            $table->boolean('is_corner')->default(false);
            $table->boolean('is_park_face')->default(false);
            $table->boolean('is_forty_feet')->default(false);
            
            $table->enum('status', ['available', 'booked', 'sold', 'cancelled'])->default('available');
            
            $table->foreignId('category_id');
        });

      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
