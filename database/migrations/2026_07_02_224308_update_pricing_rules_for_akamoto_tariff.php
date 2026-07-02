<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('pricing_rules', 'base_distance_km')) {
                $table->decimal('base_distance_km', 8, 2)
                    ->default(3)
                    ->after('vehicle_type');
            }

            if (!Schema::hasColumn('pricing_rules', 'extra_price_per_km')) {
                $table->decimal('extra_price_per_km', 12, 2)
                    ->default(200)
                    ->after('price_per_km');
            }
        });

        /*
         * Update existing pricing rules to Akamoto default logic.
         */
        DB::table('pricing_rules')->update([
            'base_distance_km' => 3,
            'base_price' => 1000,
            'extra_price_per_km' => 200,
            'commission_percentage' => 20,
            'currency' => 'RWF',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_rules', function (Blueprint $table) {
            if (Schema::hasColumn('pricing_rules', 'base_distance_km')) {
                $table->dropColumn('base_distance_km');
            }

            if (Schema::hasColumn('pricing_rules', 'extra_price_per_km')) {
                $table->dropColumn('extra_price_per_km');
            }
        });
    }
};