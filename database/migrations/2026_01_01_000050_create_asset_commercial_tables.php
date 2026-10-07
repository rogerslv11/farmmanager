<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('name');
            $table->string('asset_code')->nullable();
            $table->string('category')->default('trator');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('year', 4)->nullable();
            $table->decimal('hour_meter', 12, 1)->default(0);
            $table->date('acquired_at')->nullable();
            $table->decimal('acquisition_cost', 14, 2)->nullable();
            $table->string('status')->default('disponivel');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
            $table->foreignId('responsible_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('maintenance_type')->default('preventiva');
            $table->date('performed_at');
            $table->date('next_at')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->text('parts_used')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('concluida');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('customer_type')->default('pf');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete();
            $table->string('batch')->nullable();
            $table->decimal('quantity', 14, 3)->default(0);
            $table->string('unit')->default('sc');
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->date('sold_at');
            $table->string('status')->default('pendente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status', 'sold_at']);
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('field_id')->nullable()->constrained('fields')->nullOnDelete();
            $table->foreignId('planting_id')->nullable()->constrained('plantings')->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('agricultural_activities')->nullOnDelete();
            $table->string('description');
            $table->string('category')->default('outros');
            $table->decimal('amount', 14, 2)->default(0);
            $table->date('incurred_at');
            $table->string('status')->default('pago');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'category', 'incurred_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('equipment');
    }
};
