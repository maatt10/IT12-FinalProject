<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand the enum so 'staff' can be written
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'employee', 'staff') NOT NULL");

        // Step 2: Migrate existing 'employee' rows to 'staff'
        DB::table('users')->where('role', 'employee')->update(['role' => 'staff']);

        // Step 3: Shrink the enum to just the two valid roles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'staff') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'employee', 'staff') NOT NULL");
        DB::table('users')->where('role', 'staff')->update(['role' => 'employee']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'employee') NOT NULL");
    }
};