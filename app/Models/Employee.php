<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes, BelongsToOrganization;

    protected $fillable = ['organization_id', 'property_id', 'user_id', 'name', 'document', 'role_title', 'phone', 'email', 'hired_at', 'salary', 'status', 'notes'];

    protected function casts(): array { return ['hired_at' => 'date']; }

    public function property() { return $this->belongsTo(Property::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function activities() { return $this->belongsToMany(AgriculturalActivity::class, 'activity_employee'); }
    public function scopeActive($q) { return $q->where('status', 'ativo'); }
}
