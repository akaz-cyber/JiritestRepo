<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    private function indexExists(string $table, string $indexName): bool
    {
        $db = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$db, $table, $indexName]
        );
        return !empty($rows);
    }

    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // kolom-kolom baru dengan guard hasColumn
            if (!Schema::hasColumn('coupons','status'))       $table->enum('status',['active','inactive'])->default('active')->after('value');
            if (!Schema::hasColumn('coupons','starts_at'))    $table->timestamp('starts_at')->nullable()->after('status');
            if (!Schema::hasColumn('coupons','expires_at'))   $table->timestamp('expires_at')->nullable()->after('starts_at');
            if (!Schema::hasColumn('coupons','min_spend'))    $table->decimal('min_spend',15)->nullable()->after('expires_at');
            if (!Schema::hasColumn('coupons','max_discount')) $table->decimal('max_discount',15)->nullable()->after('min_spend');
            if (!Schema::hasColumn('coupons','usage_limit'))  $table->unsignedInteger('usage_limit')->nullable()->after('max_discount');
            if (!Schema::hasColumn('coupons','times_used'))   $table->unsignedInteger('times_used')->default(0)->after('usage_limit');
            if (!Schema::hasColumn('coupons','per_user_limit')) $table->unsignedInteger('per_user_limit')->nullable()->after('times_used');
            if (!Schema::hasColumn('coupons','applies_to'))   $table->enum('applies_to',['all','categories','products','exclude_categories','exclude_products'])->default('all')->after('per_user_limit');
            if (!Schema::hasColumn('coupons','free_shipping')) $table->boolean('free_shipping')->default(false)->after('applies_to');
        });

        // Tambah index hanya jika belum ada
        if (!$this->indexExists('coupons', 'coupons_code_unique')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->unique('code', 'coupons_code_unique');
            });
        }

        // Index lain (opsional), juga pakai guard
        if (!$this->indexExists('coupons', 'coupons_status_starts_expires_idx')) {
            DB::statement('CREATE INDEX coupons_status_starts_expires_idx ON coupons (status, starts_at, expires_at)');
        }
    }

    public function down(): void
    {
        // Drop index jika ada
        if ($this->indexExists('coupons', 'coupons_status_starts_expires_idx')) {
            DB::statement('DROP INDEX coupons_status_starts_expires_idx ON coupons');
        }
        if ($this->indexExists('coupons', 'coupons_code_unique')) {
            // unik di MySQL juga sebuah index bernama sama
            DB::statement('DROP INDEX coupons_code_unique ON coupons');
        }

        Schema::table('coupons', function (Blueprint $table) {
            // hapus kolom-kolom baru kalau ada
            if (Schema::hasColumn('coupons','free_shipping'))   $table->dropColumn('free_shipping');
            if (Schema::hasColumn('coupons','applies_to'))      $table->dropColumn('applies_to');
            if (Schema::hasColumn('coupons','per_user_limit'))  $table->dropColumn('per_user_limit');
            if (Schema::hasColumn('coupons','times_used'))      $table->dropColumn('times_used');
            if (Schema::hasColumn('coupons','usage_limit'))     $table->dropColumn('usage_limit');
            if (Schema::hasColumn('coupons','max_discount'))    $table->dropColumn('max_discount');
            if (Schema::hasColumn('coupons','min_spend'))       $table->dropColumn('min_spend');
            if (Schema::hasColumn('coupons','expires_at'))      $table->dropColumn('expires_at');
            if (Schema::hasColumn('coupons','starts_at'))       $table->dropColumn('starts_at');
            if (Schema::hasColumn('coupons','status'))          $table->dropColumn('status');
        });
    }
};
