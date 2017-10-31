<?php

namespace App\Repositories\Backend;

use App\Models\User;
use App\Repositories\BaseRepository;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;

    public function __construct()
    {
    }
}
