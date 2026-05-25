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
    Schema::create('staff_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
        $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
        $table->foreignId('treatment_id')->nullable()->constrained('treatments')->nullOnDelete();
        $table->date('assigned_date');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('staff_assignments');
}
};
