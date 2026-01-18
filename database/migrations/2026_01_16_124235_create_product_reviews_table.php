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
    Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Review details
            $table->string('reviewer_name');
            $table->string('reviewer_email');
            $table->integer('rating')->unsigned()->default(5); // 1-5 stars
            $table->string('title')->nullable();
            $table->text('comment');

            // Verification
            $table->boolean('verified_purchase')->default(false);
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');

            // Moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'spam'])->default('pending');
            $table->text('admin_reply')->nullable();
            $table->timestamp('admin_reply_at')->nullable();

            // Helpful votes
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);

            // Media
            $table->json('images')->nullable(); // Store image paths as JSON array

            $table->timestamps();

            // Indexes
            $table->index('product_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('rating');
            $table->index('created_at');
        });

        // Table to track helpful votes
        Schema::create('review_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->boolean('is_helpful'); // true = helpful, false = not helpful
            $table->timestamps();

            // Prevent duplicate votes from same user/IP
            $table->unique(['review_id', 'user_id']);
            $table->index(['review_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_helpful_votes');
        Schema::dropIfExists('product_reviews');
    }
};
