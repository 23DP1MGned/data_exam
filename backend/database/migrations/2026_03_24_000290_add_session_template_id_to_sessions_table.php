<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sessions', 'session_template_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->foreignId('session_template_id')
                    ->nullable()
                    ->after('group_id')
                    ->constrained('session_templates')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sessions', 'session_template_id')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('session_template_id');
            });
        }
    }
};
