<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_month_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('payment_item_id')->constrained('payment_items')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('month', 7);
            $table->unsignedInteger('covered_sessions_count')->default(0);
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->index(['child_id', 'group_id', 'month']);
            $table->unique('payment_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_month_coverages');
    }
};
