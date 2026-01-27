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
      Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // Discount settings
            $table->enum('discount_type', ['percentage', 'fixed', 'free_shipping'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_spend', 10, 2)->nullable();
            $table->decimal('maximum_discount', 10, 2)->nullable();

            // Usage limits
            $table->integer('usage_limit')->nullable(); // Total uses
            $table->integer('usage_limit_per_user')->nullable();
            $table->integer('usage_count')->default(0);

            // Validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Restrictions
            $table->json('product_ids')->nullable(); // Specific products
            $table->json('category_ids')->nullable(); // Specific categories
            $table->json('excluded_product_ids')->nullable();
            $table->json('excluded_category_ids')->nullable();
            $table->boolean('first_order_only')->default(false);

            $table->enum('status', ['active', 'inactive', 'scheduled', 'expired'])->default('active');
            $table->timestamps();

            $table->index('code');
            $table->index('status');
            $table->index(['starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
