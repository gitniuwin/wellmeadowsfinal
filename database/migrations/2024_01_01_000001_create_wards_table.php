<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['General', 'ICU', 'Pediatric', 'Maternity', 'Surgical', 'Orthopedic', 'Cardiac', 'Oncology', 'Emergency']);
            $table->integer('capacity');
            $table->string('floor')->nullable();
            $table->string('building')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
