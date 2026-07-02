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
        Schema::create('riders', function (Blueprint $table) {
            $table->id();

            /*
             * Rider is connected to a registered user.
             * One user can have only one rider profile.
             */
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            /*
             * Rider transport information.
             */
            $table->string('vehicle_type')->nullable(); // moto, bicycle, car, van
            $table->string('vehicle_plate_number')->nullable();
            $table->string('vehicle_color')->nullable();

            /*
             * Rider identity information.
             */
            $table->string('national_id')->nullable();
            $table->string('driving_license_number')->nullable();

            /*
             * Admin approval status.
             */
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'suspended',
            ])->default('pending');

            $table->boolean('is_online')->default(false);

            /*
             * Rider current GPS location.
             */
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->string('current_address')->nullable();
            $table->timestamp('last_location_updated_at')->nullable();

            /*
             * Admin approval/rejection tracking.
             */
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('is_online');
            $table->index(['current_latitude', 'current_longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};