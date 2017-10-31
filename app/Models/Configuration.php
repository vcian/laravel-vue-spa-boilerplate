<?php

namespace App\Models;

use Eloquent;

/**
 * Class Configuration
 * @package App\Models
 */
class Configuration extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','value'
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * @var bool
     */
    public $timestamps = false;
}
