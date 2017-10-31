<?php

namespace App\Models\User\Traits\Scope;

/**
 * Trait UserScope
 * @package App\Models\User\Traits\Scope
 */
trait UserScope {

    /**
     * @param $query
     * @return mixed
     */
    public function scopeUserProfile($query)
    {
        return $query->with('profile');
    }
}