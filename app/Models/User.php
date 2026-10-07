<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = ['name', 'email', 'password', 'organization_id', 'role', 'avatar', 'phone', 'active', 'preferences'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'active' => 'boolean'];
    }

    public function organization() { return $this->belongsTo(Organization::class); }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isGestor(): bool { return in_array($this->role, ['admin', 'gestor']); }

    public function canAccess(string $module): bool
    {
        $matrix = [
            'admin' => ['*'],
            'gestor' => ['dashboard', 'properties', 'fields', 'crops', 'plantings', 'seasons', 'activities', 'production', 'harvests', 'inventory', 'purchases', 'suppliers', 'equipment', 'maintenance', 'employees', 'customers', 'sales', 'expenses', 'reports'],
            'agronomo' => ['dashboard', 'properties', 'fields', 'crops', 'plantings', 'seasons', 'activities', 'production', 'harvests', 'reports'],
            'estoquista' => ['dashboard', 'inventory', 'purchases', 'suppliers'],
            'funcionario' => ['dashboard', 'activities'],
        ];

        $allowed = $matrix[$this->role] ?? [];
        return in_array('*', $allowed) || in_array($module, $allowed);
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
