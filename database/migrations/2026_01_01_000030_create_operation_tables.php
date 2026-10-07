<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('role_title')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('hired_at')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('status')->default('ativo');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('agricultural_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('planting_id')->nullable()->constrained('plantings')->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->foreignId('responsible_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('activity_type')->default('plantio');
            $table->date('scheduled_at')->nullable();
            $table->string('scheduled_time')->nullable();
            $table->decimal('duration_hours', 8, 2)->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('status')->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status', 'scheduled_at']);
        });

        Schema::create('activity_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agricultural_activity_id')->constrained('agricultural_activities')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unique(['agricultural_activity_id', 'employee_id'], 'activity_employee_unique');
        });

        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('planting_id')->nullable()->constrained('plantings')->nullOnDelete();
            $table->date('produced_at');
            $table->decimal('quantity', 14, 3)->default(0);
            $table->string('unit')->default('sc');
            $table->string('quality')->nullable();
            $table->string('batch')->nullable();
            $table->string('operator')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'produced_at']);
        });

        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('planting_id')->constrained('plantings')->cascadeOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->foreignId('responsible_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('harvested_at');
            $table->decimal('quantity', 14, 3)->default(0);
            $table->string('unit')->default('sc');
            $table->decimal('losses', 12, 3)->default(0);
            $table->string('quality')->nullable();
            $table->string('equipment_used')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('activity_employee');
        Schema::dropIfExists('agricultural_activities');
        Schema::dropIfExists('employees');
    }
};
