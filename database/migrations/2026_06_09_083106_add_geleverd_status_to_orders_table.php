<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite negeert enum constraints, dus enkel voor MySQL aanpassen
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('in behandeling','goedgekeurd','afgekeurd','geleverd') NOT NULL DEFAULT 'in behandeling'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('in behandeling','goedgekeurd','afgekeurd') NOT NULL DEFAULT 'in behandeling'");
        }
    }
};
