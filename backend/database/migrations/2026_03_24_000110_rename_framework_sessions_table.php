<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sessions') && ! Schema::hasTable('app_sessions')) {
            Schema::rename('sessions', 'app_sessions');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('app_sessions') && ! Schema::hasTable('sessions')) {
            Schema::rename('app_sessions', 'sessions');
        }
    }
};
