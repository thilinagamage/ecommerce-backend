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
Schema::create('product_variation_attributes', function (Blueprint $table) {
    $table->id();

    $table->foreignId('product_variation_id')
          ->constrained('product_variations')
          ->cascadeOnDelete();

    $table->foreignId('attribute_id')
          ->constrained('product_attributes')
          ->cascadeOnDelete();

    $table->foreignId('attribute_value_id')
          ->constrained('product_attribute_values')
          ->cascadeOnDelete();

    $table->timestamps();
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_attributes');
    }
};
