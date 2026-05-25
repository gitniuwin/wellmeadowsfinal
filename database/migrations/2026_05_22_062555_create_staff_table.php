<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('role', ['Doctor', 'Nurse', 'Admin', 'Manager'])->default('Nurse');
            $table->string('department')->nullable();
            $table->string('ward')->nullable();
            $table->string('shift')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
