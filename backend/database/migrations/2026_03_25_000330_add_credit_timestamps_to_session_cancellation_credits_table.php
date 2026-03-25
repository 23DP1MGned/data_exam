<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_cancellation_credits', function (Blueprint $table) {
            $table->timestamp('credited_at')->nullable()->after('amount');
            $table->timestamp('reversed_at')->nullable()->after('credited_at');
        });

        DB::table('session_cancellation_credits')
            ->whereNull('credited_at')
            ->update([
                'credited_at' => DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('session_cancellation_credits', function (Blueprint $table) {
            $table->dropColumn(['credited_at', 'reversed_at']);
        });
    }
};
