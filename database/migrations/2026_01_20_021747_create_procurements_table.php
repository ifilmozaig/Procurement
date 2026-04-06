<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['OPEX', 'CAPEX', 'CASH_ADVANCE']);
            $table->string('procurement_number')->unique();
            $table->text('reason');
            $table->enum('status', ['DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'PROCESSING', 'COMPLETED'])->default('DRAFT');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->text('specification')->nullable();
            $table->integer('quantity');
            $table->decimal('estimated_price', 15, 2);
            $table->string('vendor')->nullable();
            $table->timestamps();
        });

        Schema::create('procurement_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->enum('attachment_type', ['QUOTATION', 'PROPOSAL', 'SUPPORTING_DOC', 'PAYMENT_PROOF', 'INVOICE']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_attachments');
        Schema::dropIfExists('procurement_items');
        Schema::dropIfExists('procurements');
    }
};