<?php

namespace App\Traits;

use App\Scopes\OrganizationScope;
use App\Support\CurrentOrganization;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope(new OrganizationScope);

        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $model->organization_id = CurrentOrganization::id() ?? auth()->user()?->organization_id;
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function scopeWithoutOrgScope($query)
    {
        return $query->withoutGlobalScope(OrganizationScope::class);
    }
}
