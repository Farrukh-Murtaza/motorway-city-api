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
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('father_or_husband_name')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();

            // Contacts
            $table->string('mobile')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            // Identification
            $table->string('cnic')->unique();
            $table->date('dob');

            $table->foreignId('occupation_id');

            // Addresses
            $table->string('postal_address')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('person_img')->nullable();
            $table->string('cnic_img')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
