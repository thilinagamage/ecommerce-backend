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
        Schema::create('loyalty_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_required')->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->json('benefits')->nullable(); // Additional benefits like free shipping
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For ordering tiers
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_tiers');
    }
};
