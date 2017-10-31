<?php

namespace App\Models\User\Traits\Relationship;

use App\Models\Profile;

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
