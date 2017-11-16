<?php

namespace App\Models\Task;

use Eloquent;

class Task extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'start_date', 'due_date',
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'id';
}
