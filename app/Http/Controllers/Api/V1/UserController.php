<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Task;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Validator;

/**
 * User Controller.
 */
class UserController extends APIController
{
    /**
     * $avatar_path.
     *
     * @var string
     */
    protected $avatar_path = 'images/users/';

    /**
     * $repositery UserRepositery.
     *
     * @var object
     */
    protected $repositery;

    /**
     * @param UserRepository $repositery
     */
    public function __construct(UserRepository $repositery)
    {
        $this->repositery = $repositery;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            return $this->repositery->getAllUsers($request);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'first_name'    => 'required|min:2',
                'last_name'     => 'required|min:2',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender'        => 'required|in:male,female',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $profile = $user->Profile;

            $profile->first_name = request('first_name');
            $profile->last_name = request('last_name');
            $profile->date_of_birth = request('date_of_birth');
            $profile->gender = request('gender');
            $profile->twitter_profile = request('twitter_profile');
            $profile->facebook_profile = request('facebook_profile');
            $profile->google_plus_profile = request('google_plus_profile');
            $profile->save();

            return response()->json(['message' => 'Your profile has been updated!', 'user' => $user]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                'avatar' => 'required|image',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $profile = $user->Profile;

            if ($profile->avatar && \File::exists($this->avatar_path.$profile->avatar)) {
                \File::delete($this->avatar_path.$profile->avatar);
            }

            $extension = $request->file('avatar')->getClientOriginalExtension();
            $filename = uniqid();
            $file = $request->file('avatar')->move($this->avatar_path, $filename.'.'.$extension);
            $img = \Image::make($this->avatar_path.$filename.'.'.$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($this->avatar_path.$filename.'.'.$extension);
            $profile->avatar = $filename.'.'.$extension;
            $profile->save();

            return response()->json(['message' => 'Avatar updated!', 'profile' => $profile]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAvatar(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $profile = $user->Profile;
            if (!$profile->avatar) {
                return response()->json(['message' => 'No avatar uploaded!'], 422);
            }

            if (\File::exists($this->avatar_path.$profile->avatar)) {
                \File::delete($this->avatar_path.$profile->avatar);
            }

            $profile->avatar = null;
            $profile->save();

            return response()->json(['message' => 'Avatar removed!']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (env('IS_DEMO')) {
                return response()->json(['message' => 'You are not allowed to perform this action in this mode.'], 422);
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'Couldnot find user!'], 422);
            }

            if ($user->avatar && \File::exists($this->avatar_path.$user->avatar)) {
                \File::delete($this->avatar_path.$user->avatar);
            }

            $user->delete();

            return response()->json(['success', 'message' => 'User deleted!']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        try {
            $users_count = User::count();
            $tasks_count = Task::count();
            $recent_incomplete_tasks = Task::whereStatus(0)->orderBy('due_date', 'desc')->limit(5)->get();

            return response()->json(compact('users_count', 'tasks_count', 'recent_incomplete_tasks'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }
}
