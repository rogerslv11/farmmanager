<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'supplier_id', 'property_id', 'number', 'ordered_at', 'expected_at', 'received_at', 'discount', 'taxes', 'total', 'status', 'notes'];

    protected function casts(): array { return ['ordered_at' => 'date', 'expected_at' => 'date', 'received_at' => 'date', 'total' => 'decimal:2']; }

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function property() { return $this->belongsTo(Property::class); }
    public function items() { return $this->hasMany(PurchaseItem::class); }

    public function recalculateTotal(): void
    {
        $subtotal = $this->items()->sum('subtotal');
        $this->total = max(0, $subtotal - ($this->discount ?? 0) + ($this->taxes ?? 0));
        $this->saveQuietly();
    }
}
