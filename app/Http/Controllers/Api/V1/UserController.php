<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

/**
 * User Controller.
 */
class UserController extends APIController
{
    /**
     * $user UserRepository.
     *
     * @var object
     */
    protected $user;

    /**
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user =  $this->user->getAllUsers($request);

        return $user;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $input = $request->all();

        $response = $this->user->updateUserProfile($input);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request)
    {
        $input = $request->all();

        $response = $this->user->updateUserAvatar($input);

        return $response;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAvatar()
    {
        $response = $this->user->removeUserAvatar();

        return $response;
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $response = $this->user->deleteUser($id);

        return $response;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        $response = $this->user->dashboard();

        return $response;
    }
}
