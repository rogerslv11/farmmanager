<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'name', 'description', 'owner_name', 'document', 'address', 'city', 'state', 'total_area', 'productive_area', 'latitude', 'longitude', 'property_type', 'status'];

    protected function casts(): array
    {
        return ['total_area' => 'decimal:2', 'productive_area' => 'decimal:2', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    }

    public function fields() { return $this->hasMany(Field::class); }
    public function plantings() { return $this->hasMany(Planting::class); }
    public function seasons() { return $this->hasMany(Season::class); }
    public function equipment() { return $this->hasMany(Equipment::class); }
    public function employees() { return $this->hasMany(Employee::class); }
    public function activities() { return $this->hasMany(AgriculturalActivity::class); }
    public function expenses() { return $this->hasMany(Expense::class); }

    public function scopeActive($q) { return $q->where('status', 'ativa'); }
    public function scopeSearch($q, $term) { return $q->where(fn ($w) => $w->where('name', 'like', "%{$term}%")->orWhere('city', 'like', "%{$term}%")->orWhere('owner_name', 'like', "%{$term}%")); }
}
