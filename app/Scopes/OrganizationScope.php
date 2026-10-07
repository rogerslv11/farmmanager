<?php

namespace App\Scopes;

use App\Support\CurrentOrganization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $orgId = CurrentOrganization::id() ?? auth()->user()?->organization_id;

        if ($orgId) {
            $builder->where($model->getTable().'.organization_id', $orgId);
        }
    }
}
