<?php

namespace App\Models\Todo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Todo.
 */
class Todo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'todos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
              'todo',
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'id';
}
