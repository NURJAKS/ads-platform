<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('ad_id')->after('user_id')->constrained()->cascadeOnDelete();
            $table->string('reason')->after('ad_id');
            $table->text('comment')->nullable()->after('reason');
            $table->string('status')->default('pending')->after('comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ad_id']);
            $table->dropColumn(['user_id', 'ad_id', 'reason', 'comment', 'status']);
        });
    }
};
