<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'company_name', 'trade_name', 'document', 'phone', 'email', 'address', 'category', 'notes', 'status'];

    public function items() { return $this->hasMany(InventoryItem::class); }
    public function purchases() { return $this->hasMany(Purchase::class); }
}
