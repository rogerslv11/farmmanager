<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('insumo');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('trade_name')->nullable();
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('category')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('ativo');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_category_id')->nullable()->constrained('inventory_categories')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('unit')->default('un');
            $table->decimal('current_stock', 14, 3)->default(0);
            $table->decimal('minimum_stock', 14, 3)->default(0);
            $table->decimal('average_cost', 14, 2)->default(0);
            $table->string('location')->nullable();
            $table->string('status')->default('ativo');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->decimal('quantity', 14, 3);
            $table->string('unit')->default('un');
            $table->decimal('unit_cost', 14, 2)->nullable();
            $table->date('moved_at');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'inventory_item_id', 'moved_at']);
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('number')->nullable();
            $table->date('ordered_at')->nullable();
            $table->date('expected_at')->nullable();
            $table->date('received_at')->nullable();
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('taxes', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('status')->default('rascunho');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'status']);
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->string('unit')->default('un');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('inventory_categories');
    }
};
