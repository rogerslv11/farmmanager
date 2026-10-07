<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'document', 'email', 'phone', 'plan', 'active', 'settings'];

    protected function casts(): array
    {
        return ['active' => 'boolean', 'settings' => 'array'];
    }

    public function users() { return $this->hasMany(User::class); }
    public function properties() { return $this->hasMany(Property::class); }
}
