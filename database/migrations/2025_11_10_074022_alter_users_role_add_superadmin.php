<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Tambah opsi 'superadmin' ke enum (MySQL/MariaDB)
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user','superadmin') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        // Kembalikan seperti semula (tanpa superadmin)
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user') NOT NULL DEFAULT 'user'");
    }
};