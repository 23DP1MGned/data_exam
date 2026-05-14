<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->unsignedInteger('group_number')->nullable()->after('name');
        });

        DB::table('groups')
            ->orderBy('id')
            ->get()
            ->each(function ($group) {
                DB::table('groups')
                    ->where('id', $group->id)
                    ->update(['group_number' => $group->id]);
            });

        Schema::table('groups', function (Blueprint $table) {
            $table->unique('group_number');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropUnique(['group_number']);
            $table->dropColumn('group_number');
        });
    }
};
