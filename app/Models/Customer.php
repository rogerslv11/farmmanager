<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'name', 'document', 'email', 'phone', 'address', 'customer_type', 'notes'];

    public function sales() { return $this->hasMany(Sale::class); }
}
