<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'property_id', 'name', 'code', 'area', 'soil_type', 'irrigation', 'location', 'status', 'notes'];

    protected function casts(): array { return ['area' => 'decimal:2']; }

    public function property() { return $this->belongsTo(Property::class); }
    public function plantings() { return $this->hasMany(Planting::class); }
    public function activities() { return $this->hasMany(AgriculturalActivity::class); }
    public function productions() { return $this->hasMany(Production::class); }

    public function currentPlanting() { return $this->hasOne(Planting::class)->whereNotIn('status', ['colhida', 'encerrada'])->latestOfMany(); }
    public function scopeActive($q) { return $q->where('status', 'ativo'); }
}
