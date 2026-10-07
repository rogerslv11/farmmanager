<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'planting_id', 'field_id', 'crop_id', 'responsible_id', 'harvested_at', 'quantity', 'unit', 'losses', 'quality', 'equipment_used', 'notes'];

    protected function casts(): array { return ['harvested_at' => 'date', 'quantity' => 'decimal:3']; }

    public function planting() { return $this->belongsTo(Planting::class); }
    public function field() { return $this->belongsTo(Field::class); }
    public function crop() { return $this->belongsTo(Crop::class); }
    public function responsible() { return $this->belongsTo(Employee::class, 'responsible_id'); }
}
