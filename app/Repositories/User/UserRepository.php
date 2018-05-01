<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepository;
use App\Models\User\User;
use App\Models\Task\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use Validator;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * $avatar_path.
     *
     * @var string
     */
    protected $avatar_path = 'images/users/';

    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;

    public function getAllUsers($request)
    {
        try {

            $users = User::userProfile();

            if ($request->has('first_name')) {
                $users->whereHas('profile', function ($q) use ($request) {
                    $q->where('first_name', 'like', '%'.request('first_name').'%');
                });
            }

            if ($request->has('last_name')) {
                $users->whereHas('profile', function ($q) use ($request) {
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

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function updateUserProfile($input = null)
    {
        DB::beginTransaction();

        try {

            $validation = Validator::make($input, [
                'first_name'    => 'required|min:2',
                'last_name'     => 'required|min:2',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender'        => 'required|in:male,female',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $profile = $user->profile;

            $profile->first_name = request('first_name');
            $profile->last_name = request('last_name');
            $profile->date_of_birth = request('date_of_birth');
            $profile->gender = request('gender');
            $profile->twitter_profile = request('twitter_profile');
            $profile->facebook_profile = request('facebook_profile');
            $profile->google_plus_profile = request('google_plus_profile');

            if ($profile->save()) {
                DB::commit();
                $responseArr = [
                    'message' => 'Your profile has been updated!',
                    'user'    => $user,
                ];
            } else {
                DB::rollback();
                $responseArr = [
                    'message' => 'Something went wrong!',
                ];
            }

            return response()->json($responseArr);

        } catch (\Exception $ex) {

            DB::rollback();
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function updateUserAvatar($input = null)
    {
        DB::beginTransaction();

        try {

            $validation = Validator::make($input, [
                'avatar' => 'required|image',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = JWTAuth::parseToken()->authenticate();
            $profile = $user->profile;

            if ($profile->avatar && \File::exists($this->avatar_path.$profile->avatar)) {
                \File::delete($this->avatar_path.$profile->avatar);
            }

            $extension = request()->file('avatar')->getClientOriginalExtension();
            $filename = uniqid();
            $file = request()->file('avatar')->move($this->avatar_path, $filename.'.'.$extension);
            $img = \Image::make($this->avatar_path.$filename.'.'.$extension);
            $img->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($this->avatar_path.$filename.'.'.$extension);
            $profile->avatar = $filename.'.'.$extension;

            if ($profile->save()) {
                DB::commit();
                $responseArr = [
                    'message' => 'Your avatar has been updated!',
                    'profile' => $profile,
                ];
            } else {
                DB::rollback();
                $responseArr = [
                    'message' => 'Something went wrong!',
                ];
            }

            return response()->json($responseArr);

        } catch (\Exception $ex) {

            DB::rollback();
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function removeUserAvatar()
    {
        DB::beginTransaction();

        try {

            $user = JWTAuth::parseToken()->authenticate();

            $profile = $user->profile;

            if (!$profile->avatar) {
                return response()->json(['message' => 'No avatar uploaded!'], 422);
            }

            if (\File::exists($this->avatar_path.$profile->avatar)) {
                \File::delete($this->avatar_path.$profile->avatar);
            }

            $profile->avatar = null;

            if ($profile->save()) {
                DB::commit();
                $responseArr = [
                    'message' => 'Avatar has been removed successfully!',
                ];
            } else {
                DB::rollback();
                $responseArr = [
                    'message' => 'Something went wrong!',
                ];
            }

            return response()->json($responseArr);

        } catch (\Exception $ex) {

            DB::rollback();
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    public function deleteUser($id = null)
    {
        DB::beginTransaction();

        try {

            if (env('IS_DEMO')) {
                return response()->json(['message' => 'You are not allowed to perform this action in this mode.'], 422);
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'Could not find user!'], 422);
            }

            if ($user->avatar && \File::exists($this->avatar_path.$user->avatar)) {
                \File::delete($this->avatar_path.$user->avatar);
            }

            if ($user->delete()) {
                DB::commit();
                $responseArr = [
                    'message' => 'User has been deleted successfully!',
                ];
            } else {
                DB::rollback();
                $responseArr = [
                    'message' => 'Something went wrong!',
                ];
            }

            return response()->json(['success', 'message' => $responseArr]);

        } catch (\Exception $ex) {

            DB::rollback();
            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

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
