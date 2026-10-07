<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $table = 'equipment';

    protected $fillable = ['organization_id', 'property_id', 'name', 'asset_code', 'category', 'brand', 'model', 'year', 'hour_meter', 'acquired_at', 'acquisition_cost', 'status', 'notes'];

    protected function casts(): array { return ['acquired_at' => 'date']; }

    public function property() { return $this->belongsTo(Property::class); }
    public function maintenances() { return $this->hasMany(MaintenanceRecord::class); }
    public function nextMaintenance() { return $this->hasOne(MaintenanceRecord::class)->whereNotNull('next_at')->latestOfMany('next_at'); }
}
