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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
        $table->foreignId('doctor_id')->constrained('staff')->cascadeOnDelete();
        $table->dateTime('appointment_date');
        $table->enum('type', ['Consultation', 'Follow-up', 'Emergency', 'Routine Check'])->default('Consultation');
        $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('appointments');
}
};
