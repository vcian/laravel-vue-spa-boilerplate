<?php
namespace App\Models\Profile\Traits\Relationship;

use App\Models\User;

/**
 * Trait ProfileRelationship
 * @package App\Models\Profile\Traits\Relationship
 */
trait ProfileRelationship {

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}