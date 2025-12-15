<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ad_moderation_logs', function (Blueprint $table) {
            $table->string('action')->nullable()->after('admin_id');
            $table->text('reason')->nullable()->after('action');
        });
        
        // Заполняем action на основе new_status для существующих записей
        \DB::table('ad_moderation_logs')->whereNull('action')->update([
            'action' => \DB::raw("CASE 
                WHEN new_status = 'approved' THEN 'approve'
                WHEN new_status = 'rejected' THEN 'reject'
                ELSE 'moderate'
            END")
        ]);
    }

    public function down(): void
    {
        Schema::table('ad_moderation_logs', function (Blueprint $table) {
            $table->dropColumn(['action', 'reason']);
        });
    }
};
