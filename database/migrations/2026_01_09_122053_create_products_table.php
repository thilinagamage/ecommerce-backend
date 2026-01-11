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

            $table->string('name');
            $table->string('slug')->unique();

            $table->text('description')->nullable();
            $table->text('short_description')->nullable();

            $table->enum('type', ['simple', 'variable'])->default('simple');

            $table->decimal('price', 10, 2)->nullable(); // used for simple product
            $table->decimal('sale_price', 10, 2)->nullable();

            $table->enum('status', ['draft', 'published', 'inactive'])->default('draft');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

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
