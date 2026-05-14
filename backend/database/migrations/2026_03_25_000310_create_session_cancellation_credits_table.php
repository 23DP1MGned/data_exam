<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_cancellation_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('child_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('payment_item_id')->nullable()->constrained('payment_items')->nullOnDelete();
            $table->foreignId('payment_month_coverage_id')->nullable()->constrained('payment_month_coverages')->nullOnDelete();
            $table->string('source_type');
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->unique(['session_id', 'child_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_cancellation_credits');
    }
};
