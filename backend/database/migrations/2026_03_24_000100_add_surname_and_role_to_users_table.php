<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->default('User')->after('name');
            $table->string('role')->default(User::ROLE_CHILD)->after('password');
        });

        DB::table('users')->update([
            'surname' => 'User',
            'role' => User::ROLE_CHILD,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['surname', 'role']);
        });
    }
};
