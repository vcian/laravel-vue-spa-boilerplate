<?php

namespace App\Models\Profile\Traits\Relationship;

use App\Models\User;

/**
 * Trait ProfileRelationship.
 */
trait ProfileRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
