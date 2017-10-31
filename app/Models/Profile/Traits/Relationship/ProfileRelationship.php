<?php

namespace App\Models\Profile\Traits\Relationship;

use App\Models\User;

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
