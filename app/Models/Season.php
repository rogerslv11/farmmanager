<?php

namespace App\Models;

use App\Enums\SeasonStatus;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'property_id', 'crop_id', 'name', 'year', 'starts_at', 'ends_at', 'total_area', 'estimated_production', 'realized_production', 'estimated_cost', 'realized_cost', 'estimated_revenue', 'status', 'notes'];

    protected function casts(): array
    {
        return ['starts_at' => 'date', 'ends_at' => 'date', 'total_area' => 'decimal:2', 'estimated_production' => 'decimal:2', 'realized_production' => 'decimal:2', 'estimated_cost' => 'decimal:2', 'realized_cost' => 'decimal:2', 'estimated_revenue' => 'decimal:2'];
    }

    public function property() { return $this->belongsTo(Property::class); }
    public function crop() { return $this->belongsTo(Crop::class); }
    public function plantings() { return $this->hasMany(Planting::class); }
    public function activities() { return $this->hasMany(AgriculturalActivity::class); }
    public function productions() { return $this->hasMany(Production::class); }
    public function expenses() { return $this->hasMany(Expense::class); }

    public function getProgressAttribute(): float
    {
        if (! $this->starts_at || ! $this->ends_at) return 0;
        $total = $this->starts_at->diffInDays($this->ends_at) ?: 1;
        $done = $this->starts_at->diffInDays(min(now(), $this->ends_at));
        return round(min(100, max(0, $done / $total * 100)), 1);
    }

    public function getProductivityAttribute(): float
    {
        return $this->total_area > 0 ? round($this->realized_production / $this->total_area, 2) : 0;
    }

    public function getMarginAttribute(): float
    {
        return round(($this->estimated_revenue ?? 0) - ($this->realized_cost ?? 0), 2);
    }

    public function scopeActive($q) { return $q->whereIn('status', [SeasonStatus::EmAndamento->value, SeasonStatus::Colheita->value]); }
}
