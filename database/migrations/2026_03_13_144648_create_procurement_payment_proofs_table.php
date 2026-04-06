<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel baru: menyimpan struk & realisasi per perusahaan (dinamis)
        Schema::create('procurement_payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('payment_proof')->nullable();         // path file struk
            $table->unsignedBigInteger('realisasi_amount')->default(0);
            $table->timestamps();

            $table->unique(['procurement_id', 'company_id']);   // 1 struk per perusahaan per procurement
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_payment_proofs');
    }
};