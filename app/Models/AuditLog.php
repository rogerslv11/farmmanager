<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'user_id', 'action', 'auditable_type', 'auditable_id', 'description', 'old_values', 'new_values', 'ip'];

    protected function casts(): array { return ['old_values' => 'array', 'new_values' => 'array']; }

    public function user() { return $this->belongsTo(User::class); }
    public function organization() { return $this->belongsTo(Organization::class); }
}
