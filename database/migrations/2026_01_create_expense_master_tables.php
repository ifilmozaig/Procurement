<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          
            $table->string('code')->nullable();              
            $table->integer('sort_order')->default(0);       
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('expense_master_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')
                  ->constrained('expense_categories')
                  ->onDelete('cascade');
            $table->string('item_name');                     
            $table->text('specification')->nullable();       
            $table->string('unit')->nullable();              
            $table->decimal('estimated_price', 15, 2)->default(0); 
            $table->string('vendor')->nullable();            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_budget_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_master_item_id')
                  ->constrained('expense_master_items')
                  ->onDelete('cascade');
            $table->enum('company', ['konnco', 'kodemee', 'both'])->default('both');
            $table->integer('planned_qty')->default(0);     
            $table->decimal('planned_price', 15, 2)->default(0); 
            $table->string('period')->nullable();            
            $table->timestamps();
        });

        Schema::table('procurement_items', function (Blueprint $table) {
            $table->foreignId('expense_master_item_id')
                  ->nullable()
                  ->after('procurement_id')
                  ->constrained('expense_master_items')
                  ->nullOnDelete();
            $table->enum('company_target', ['konnco', 'kodemee', 'both'])
                  ->default('both')
                  ->after('vendor');
            $table->boolean('is_purchased')->default(false)->after('company_target');
            $table->timestamp('purchased_at')->nullable()->after('is_purchased');
            $table->foreignId('purchased_by')->nullable()->after('purchased_at')
                  ->constrained('users')->nullOnDelete();
            $table->text('purchase_notes')->nullable()->after('purchased_by');
        });
    }

    public function down(): void
    {
        Schema::table('procurement_items', function (Blueprint $table) {
            $table->dropForeign(['expense_master_item_id']);
            $table->dropForeign(['purchased_by']);
            $table->dropColumn([
                'expense_master_item_id',
                'company_target',
                'is_purchased',
                'purchased_at',
                'purchased_by',
                'purchase_notes',
            ]);
        });

        Schema::dropIfExists('expense_budget_plans');
        Schema::dropIfExists('expense_master_items');
        Schema::dropIfExists('expense_categories');
    }
};