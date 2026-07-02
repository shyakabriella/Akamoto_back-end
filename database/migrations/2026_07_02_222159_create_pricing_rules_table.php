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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();

            /*
             * Rule name example:
             * Kigali Moto Standard
             * Default City Delivery
             */
            $table->string('name');

            /*
             * Vehicle type can be specific or null.
             * null means this rule is general/default.
             */
            $table->enum('vehicle_type', [
                'moto',
                'bicycle',
                'car',
                'van',
            ])->nullable();

            /*
             * Pricing values.
             *
             * Example:
             * base_price = 1000
             * price_per_km = 500
             * minimum_price = 1500
             */
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('price_per_km', 12, 2)->default(0);
            $table->decimal('minimum_price', 12, 2)->default(0);

            /*
             * Akamoto commission percentage.
             *
             * Example:
             * commission_percentage = 20
             * Customer pays 5000
             * Akamoto takes 1000
             * Rider earns 4000
             */
            $table->decimal('commission_percentage', 5, 2)->default(0);

            $table->string('currency', 10)->default('RWF');

            /*
             * Only active pricing rules are used for price calculation.
             */
            $table->boolean('is_active')->default(false);

            /*
             * Admin tracking.
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('vehicle_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};