<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'inventory_item_id', 'property_id', 'user_id', 'type', 'quantity', 'unit', 'unit_cost', 'moved_at', 'reason', 'notes'];

    protected function casts(): array { return ['moved_at' => 'date', 'quantity' => 'decimal:3']; }

    public function item() { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
    public function property() { return $this->belongsTo(Property::class); }
    public function user() { return $this->belongsTo(User::class); }
}
