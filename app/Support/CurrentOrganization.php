<?php

namespace App\Support;

use App\Models\Organization;
use Illuminate\Support\Facades\Session;

class CurrentOrganization
{
    public static function id(): ?int
    {
        if (Session::has('organization_id')) {
            return (int) Session::get('organization_id');
        }

        return auth()->user()?->organization_id;
    }

    public static function get(): ?Organization
    {
        $id = static::id();

        return $id ? Organization::find($id) : null;
    }

    public static function set(int $organizationId): void
    {
        Session::put('organization_id', $organizationId);
    }
}
