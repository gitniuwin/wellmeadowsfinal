<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->enum('service_type', ['room', 'treatment', 'services']);
            $table->decimal('total_amount', 10, 2);
            $table->date('due_date');
            $table->enum('status', ['paid', 'pending', 'overdue'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
