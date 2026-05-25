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
    $table->string('ward_name');
    $table->string('ward_type');
    $table->integer('capacity')->default(0);
    $table->integer('occupied_beds')->default(0);
    $table->string('charge_nurse')->nullable();
    $table->string('floor')->nullable();
    $table->enum('availability_status', ['Available', 'Limited', 'Full'])->default('Available');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
