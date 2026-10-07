<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgriculturalActivity extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = ['organization_id', 'property_id', 'field_id', 'planting_id', 'season_id', 'responsible_id', 'title', 'description', 'activity_type', 'scheduled_at', 'scheduled_time', 'duration_hours', 'cost', 'status', 'notes'];

    protected function casts(): array { return ['scheduled_at' => 'date', 'cost' => 'decimal:2']; }

    public function property() { return $this->belongsTo(Property::class); }
    public function field() { return $this->belongsTo(Field::class); }
    public function planting() { return $this->belongsTo(Planting::class); }
    public function season() { return $this->belongsTo(Season::class); }
    public function responsible() { return $this->belongsTo(Employee::class, 'responsible_id'); }
    public function employees() { return $this->belongsToMany(Employee::class, 'activity_employee'); }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['pendente', 'agendada']) && $this->scheduled_at && $this->scheduled_at->isPast();
    }

    public function scopeOverdue($q) { return $q->whereIn('status', ['pendente', 'agendada'])->whereDate('scheduled_at', '<', now()); }
    public function scopeUpcoming($q) { return $q->whereIn('status', ['pendente', 'agendada'])->whereDate('scheduled_at', '>=', now())->orderBy('scheduled_at'); }
}
