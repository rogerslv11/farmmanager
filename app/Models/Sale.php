<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'customer_id', 'crop_id', 'batch', 'quantity', 'unit', 'unit_price', 'discount', 'total', 'sold_at', 'status', 'notes'];

    protected function casts(): array { return ['sold_at' => 'date', 'total' => 'decimal:2', 'quantity' => 'decimal:3']; }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function crop() { return $this->belongsTo(Crop::class); }
}
