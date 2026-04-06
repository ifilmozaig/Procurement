<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('vendor')
                ->constrained('vendors')
                ->nullOnDelete();

            $table->string('payment_method')->nullable()->after('vendor_id');

            $table->foreignId('bank_account_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('vendor_bank_accounts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['bank_account_id']);
            $table->dropColumn(['vendor_id', 'payment_method', 'bank_account_id']);
        });
    }
};