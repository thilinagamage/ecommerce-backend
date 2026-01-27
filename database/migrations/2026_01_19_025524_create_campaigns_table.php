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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->text('content');
            $table->enum('type', ['email', 'sms', 'notification'])->default('email');
            $table->enum('status', ['draft', 'scheduled', 'sent', 'cancelled'])->default('draft');

            // Targeting
            $table->json('segment_filters')->nullable(); // Customer segmentation rules
            $table->integer('recipient_count')->default(0);

            // Scheduling
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            // Performance tracking
            $table->integer('sent_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('converted_count')->default(0);
            $table->decimal('revenue_generated', 10, 2)->default(0);

            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
