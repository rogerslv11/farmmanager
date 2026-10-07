<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'equipment_id', 'responsible_id', 'maintenance_type', 'performed_at', 'next_at', 'cost', 'parts_used', 'description', 'status'];

    protected function casts(): array { return ['performed_at' => 'date', 'next_at' => 'date', 'cost' => 'decimal:2']; }

    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function responsible() { return $this->belongsTo(Employee::class, 'responsible_id'); }

    public function scopeOverdue($q) { return $q->whereNotNull('next_at')->whereDate('next_at', '<', now()); }
    public function scopeUpcoming($q) { return $q->whereNotNull('next_at')->whereBetween('next_at', [now(), now()->addDays(30)]); }
}
