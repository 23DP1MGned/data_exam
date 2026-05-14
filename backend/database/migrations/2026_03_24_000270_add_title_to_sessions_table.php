<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sessions', 'title')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->string('title')->nullable()->after('group_id');
            });
        }

        DB::table('groups')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get()
            ->each(function ($group) {
                DB::table('sessions')
                    ->where('group_id', $group->id)
                    ->whereNull('title')
                    ->update(['title' => $group->name]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('sessions', 'title')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
};
