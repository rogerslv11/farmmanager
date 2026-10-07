<?php

namespace App\Models;

use App\Enums\PlantingStatus;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planting extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'property_id', 'field_id', 'crop_id', 'season_id', 'planted_at', 'harvest_forecast_at', 'harvested_at', 'planted_area', 'seed_quantity', 'estimated_yield', 'realized_yield', 'status', 'notes'];

    protected function casts(): array
    {
        return ['planted_at' => 'date', 'harvest_forecast_at' => 'date', 'harvested_at' => 'date', 'planted_area' => 'decimal:2'];
    }

    public function property() { return $this->belongsTo(Property::class); }
    public function field() { return $this->belongsTo(Field::class); }
    public function crop() { return $this->belongsTo(Crop::class); }
    public function season() { return $this->belongsTo(Season::class); }
    public function activities() { return $this->hasMany(AgriculturalActivity::class); }
    public function productions() { return $this->hasMany(Production::class); }
    public function harvests() { return $this->hasMany(Harvest::class); }

    public function getProductivityAttribute(): float
    {
        return $this->planted_area > 0 ? round(($this->realized_yield ?? 0) / $this->planted_area, 2) : 0;
    }

    public function scopeActive($q) { return $q->whereNotIn('status', [PlantingStatus::Colhida->value, PlantingStatus::Encerrada->value]); }
}
