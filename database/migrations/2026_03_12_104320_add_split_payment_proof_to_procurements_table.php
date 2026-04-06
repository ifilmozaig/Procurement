<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->string('payment_proof_konnco')->nullable()->after('payment_proof_uploaded_at');
            $table->string('payment_proof_kodemee')->nullable()->after('payment_proof_konnco');
            $table->decimal('realisasi_amount_konnco', 15, 2)->nullable()->after('payment_proof_kodemee');
            $table->decimal('realisasi_amount_kodemee', 15, 2)->nullable()->after('realisasi_amount_konnco');
        });
    }

    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropColumn([
                'payment_proof_konnco',
                'payment_proof_kodemee',
                'realisasi_amount_konnco',
                'realisasi_amount_kodemee',
            ]);
        });
    }
};