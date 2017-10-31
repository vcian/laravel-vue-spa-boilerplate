<?php

namespace App\Models\User\Traits\Relationship;

use App\Models\Profile;

/**
 * Trait UserRelationship
 * @package App\Models\User\Traits\Relationship
 */
trait UserRelationship {

    /**
     * One-to-One relationship with profile.
     *
     * @return mixed
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
