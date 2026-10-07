<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'inventory_item_id', 'quantity', 'unit', 'unit_price', 'discount', 'subtotal'];

    public function purchase() { return $this->belongsTo(Purchase::class); }
    public function item() { return $this->belongsTo(InventoryItem::class, 'inventory_item_id'); }
}
