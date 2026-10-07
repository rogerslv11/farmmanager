<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'inventory_category_id', 'supplier_id', 'name', 'sku', 'unit', 'current_stock', 'minimum_stock', 'average_cost', 'location', 'status'];

    protected function casts(): array { return ['current_stock' => 'decimal:3', 'minimum_stock' => 'decimal:3', 'average_cost' => 'decimal:2']; }

    public function category() { return $this->belongsTo(InventoryCategory::class, 'inventory_category_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function movements() { return $this->hasMany(InventoryMovement::class); }

    public function getIsLowStockAttribute(): bool { return $this->current_stock <= $this->minimum_stock; }
    public function getStockValueAttribute(): float { return round($this->current_stock * $this->average_cost, 2); }
    public function scopeLowStock($q) { return $q->whereColumn('current_stock', '<=', 'minimum_stock'); }
    public function scopeSearch($q, $term) { return $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%")); }
}
