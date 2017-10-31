<?php

namespace App\Models;

use App\Models\Profile\Traits\Relationship\ProfileRelationship;
use Eloquent;

/**
 * Class Profile
 * @package App\Models
 */
class Profile extends Eloquent
{
    use ProfileRelationship;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','first_name','last_name','gender','date_of_birth','facebook_profile','twitter_profile','google_plus_profile','avatar'
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'id';
}
