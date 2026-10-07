<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('document')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->decimal('total_area', 12, 2)->default(0);
            $table->decimal('productive_area', 12, 2)->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('property_type')->default('fazenda');
            $table->string('status')->default('ativa');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('average_cycle_days')->nullable();
            $table->string('production_unit')->default('sc');
            $table->decimal('expected_yield', 10, 2)->nullable();
            $table->string('status')->default('ativa');
            $table->timestamps();
        });

        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->decimal('area', 12, 2)->default(0);
            $table->string('soil_type')->nullable();
            $table->string('irrigation')->nullable();
            $table->text('location')->nullable();
            $table->string('status')->default('ativo');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'property_id']);
        });

        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete();
            $table->string('name');
            $table->string('year', 9)->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('total_area', 12, 2)->default(0);
            $table->decimal('estimated_production', 12, 2)->default(0);
            $table->decimal('realized_production', 12, 2)->default(0);
            $table->decimal('estimated_cost', 14, 2)->default(0);
            $table->decimal('realized_cost', 14, 2)->default(0);
            $table->decimal('estimated_revenue', 14, 2)->default(0);
            $table->string('status')->default('planejada');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('plantings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->date('planted_at')->nullable();
            $table->date('harvest_forecast_at')->nullable();
            $table->date('harvested_at')->nullable();
            $table->decimal('planted_area', 12, 2)->default(0);
            $table->decimal('seed_quantity', 12, 2)->nullable();
            $table->decimal('estimated_yield', 12, 2)->nullable();
            $table->decimal('realized_yield', 12, 2)->nullable();
            $table->string('status')->default('planejada');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'status', 'season_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantings');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('crops');
        Schema::dropIfExists('properties');
    }
};
