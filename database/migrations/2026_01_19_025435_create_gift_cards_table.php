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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('initial_balance', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->foreignId('purchaser_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('recipient_email')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['active', 'redeemed', 'expired', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
