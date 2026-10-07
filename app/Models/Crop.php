<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'name', 'code', 'description', 'average_cycle_days', 'production_unit', 'expected_yield', 'status'];

    public function plantings() { return $this->hasMany(Planting::class); }
    public function seasons() { return $this->hasMany(Season::class); }
    public function productions() { return $this->hasMany(Production::class); }
}
