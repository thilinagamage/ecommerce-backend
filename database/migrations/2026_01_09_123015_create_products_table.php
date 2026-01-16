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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('name');
            $table->string('slug')->unique();

            // Descriptions
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // Product type (future-ready)
            $table->enum('product_type', ['simple', 'variable'])->default('simple');

            // Status & visibility
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->enum('visibility', ['shop', 'search', 'both', 'hidden'])->default('both');

            // Collection (you already built this)
            $table->foreignId('collection_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
