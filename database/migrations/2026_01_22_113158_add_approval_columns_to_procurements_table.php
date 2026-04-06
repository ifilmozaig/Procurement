<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('finance_comment')->nullable()->after('rejection_reason');
            $table->foreignId('forwarded_by')->nullable()->after('finance_comment')->constrained('users')->nullOnDelete();
            $table->timestamp('forwarded_at')->nullable()->after('forwarded_by');
            $table->foreignId('approved_by_manager')->nullable()->after('forwarded_at')->constrained('users')->nullOnDelete();
            $table->timestamp('manager_approved_at')->nullable()->after('approved_by_manager');
            $table->text('manager_comment')->nullable()->after('manager_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('procurements', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['forwarded_by']);
            $table->dropForeign(['approved_by_manager']);
            
            $table->dropColumn([
                'reviewed_by',
                'reviewed_at',
                'finance_comment',
                'forwarded_by',
                'forwarded_at',
                'approved_by_manager',
                'manager_approved_at',
                'manager_comment',
            ]);
        });
    }
};