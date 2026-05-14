<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('unique_key')->nullable()->after('type');
            $table->json('payload')->nullable()->after('unique_key');
            $table->unique(['user_id', 'unique_key']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropUnique('notifications_user_id_unique_key_unique');
            $table->dropColumn(['unique_key', 'payload']);
        });
    }
};
