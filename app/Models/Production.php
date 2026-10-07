<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'productions';

    protected $fillable = ['organization_id', 'crop_id', 'season_id', 'field_id', 'planting_id', 'produced_at', 'quantity', 'unit', 'quality', 'batch', 'operator', 'notes'];

    protected function casts(): array { return ['produced_at' => 'date', 'quantity' => 'decimal:3']; }

    public function crop() { return $this->belongsTo(Crop::class); }
    public function season() { return $this->belongsTo(Season::class); }
    public function field() { return $this->belongsTo(Field::class); }
    public function planting() { return $this->belongsTo(Planting::class); }
}
