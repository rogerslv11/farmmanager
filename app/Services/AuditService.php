<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    public static function log(string $action, ?Model $model, string $description, array $old = [], array $new = []): void
    {
        AuditLog::create([
            'organization_id' => auth()->user()?->organization_id ?? $model?->getAttribute('organization_id'),
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model?->getKey(),
            'description' => $description,
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip' => request()->ip(),
        ]);
    }
}
