<?php

namespace App\Repositories;

use App\Models\User;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;

    /**
     * [__construct description].
     */
    public function __construct()
    {
    }

    public function getAllUsers($request)
    {
        $users = User::with('profile');

        if ($request->has('first_name')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.request('first_name').'%');
            });
        }

        if ($request->has('last_name')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('last_name', 'like', '%'.request('last_name').'%');
            });
        }

        if ($request->has('email')) {
            $users->where('email', 'like', '%'.request('email').'%');
        }

        if ($request->has('status')) {
            $users->whereStatus(request('status'));
        }

        if (request('sortBy') == 'first_name' || request('sortBy') == 'last_name') {
            $users->with(['profile' => function ($q) {
                $q->orderBy(request('sortBy'), request('order'));
            }]);
        } else {
            $users->orderBy(request('sortBy'), request('order'));
        }

        return $users->paginate(request('pageLength'));
    }
}
