<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'name', 'type', 'description'];

    public function items() { return $this->hasMany(InventoryItem::class); }
}
