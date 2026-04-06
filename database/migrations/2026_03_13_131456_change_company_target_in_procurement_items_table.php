<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            // Hapus kolom lama, buat FK baru
            $table->dropColumn('company_target');
        });

        Schema::table('procurement_items', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('vendor_id')
                ->constrained('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->string('company_target')->nullable();
        });
    }
};