<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE staff DROP CONSTRAINT IF EXISTS staff_role_check');
        DB::statement('ALTER TABLE staff ALTER COLUMN role TYPE VARCHAR(255)');
    }

    public function down(): void {}
};