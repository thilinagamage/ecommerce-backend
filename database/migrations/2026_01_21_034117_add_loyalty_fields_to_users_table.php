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
   Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'loyalty_points')) {
                $table->integer('loyalty_points')->default(0)->after('email');
            }

            if (!Schema::hasColumn('users', 'loyalty_tier_id')) {
                $table->foreignId('loyalty_tier_id')->nullable()->after('loyalty_points')
                    ->constrained('loyalty_tiers')->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'total_points_earned')) {
                $table->integer('total_points_earned')->default(0)->after('loyalty_tier_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['loyalty_tier_id']);
            $table->dropColumn(['loyalty_points', 'loyalty_tier_id', 'total_points_earned']);
        });
    }
};
