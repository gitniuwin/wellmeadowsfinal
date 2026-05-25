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
    Schema::create('treatments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
        $table->foreignId('doctor_id')->constrained('staff')->cascadeOnDelete();
        $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
        $table->string('diagnosis');
        $table->string('procedure');
        $table->date('treatment_date');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('treatments');
}
};
