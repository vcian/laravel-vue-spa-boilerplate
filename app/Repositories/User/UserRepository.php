<?php

namespace App\Repositories\User;

use App\Models\Profile\Profile;
use App\Repositories\BaseRepository;
use App\Notifications\Activation;
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

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers($request)
    {
        try {

            $users = User::select('users.*', \DB::raw('(SELECT first_name FROM profiles WHERE profiles.user_id = users.id ) as first_name, (SELECT last_name FROM profiles WHERE profiles.user_id = users.id ) as last_name'))->userProfile();

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

            $users->orderBy(request('sortBy'), request('order'));

            return $users->paginate(request('pageLength'));

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param null $input
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param null $input
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
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

            return response()->json($responseArr);

        } catch (\Exception $ex) {

            DB::rollback();
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

    /**
     * @param null $input
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeUser($input = null)
    {
        try {

            $validation = Validator::make($input, [
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email|unique:users',
                'password'      => 'required',
                'date_of_birth' => 'required|date_format:Y-m-d',
                'gender'        => 'required',
                'status'        => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = User::create([
                'email'    => request('email'),
                'status'   => request('status'),
                'password' => bcrypt(request('password')),
            ]);
            if (request('status') == 'pending_activation')
                $user->activation_token = generateUuid();
            $user->save();

            $profile = new Profile();
            $profile->first_name = request('first_name');
            $profile->last_name = request('last_name');
            $profile->date_of_birth = request('date_of_birth');
            $profile->gender = request('gender');
            $user->profile()->save($profile);

            if (request('status') == 'pending_activation')
                $user->notify(new Activation($user));

            return response()->json(['message' => 'User added!']);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param null $id
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function showUser($id = null)
    {
        try {

            //$users = User::userProfile();
            $user = User::userProfile()->whereId($id)->first();

            if (!$user) {
                return response()->json(['message' => 'Could not find user!'], 422);
            }

            $user = array(
                'first_name'    => isset($user->profile->first_name) ? $user->profile->first_name : null,
                'last_name'     => isset($user->profile->last_name) ? $user->profile->last_name : null,
                'email'         => isset($user->email) ? $user->email : null,
                'date_of_birth' => isset($user->profile->date_of_birth) ? $user->profile->date_of_birth : null,
                'gender'        => isset($user->profile->gender) ? $user->profile->gender : null,
                'status'        => isset($user->status) ? $user->status : null,
            );

            return $user;

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }

    /**
     * @param null $input
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser($input = null, $id = null)
    {
        try {

            $validation = Validator::make($input, [
                'first_name'            => 'required',
                'last_name'             => 'required',
                'email'                 => 'required|email|unique:users,email,'.$id,
                'date_of_birth'         => 'required|date_format:Y-m-d',
                'gender'                => 'required',
                'status'                => 'required',
            ]);

            if ($validation->fails()) {
                return response()->json(['message' => $validation->messages()->first()], 422);
            }

            $user = User::userProfile()->whereId($id)->first(); // get user details with profile

            $user->email = request('email');
            $user->status = request('status');
            if (request('password'))
                $user->password = bcrypt(request('password'));
            if (request('status') == 'pending_activation')
                $user->activation_token = generateUuid();   // generate uuid if status not active

            $user->save();  // save the user details

            $profile = $user->profile;  // get profile details

            $profile->first_name = request('first_name');
            $profile->last_name = request('last_name');
            $profile->date_of_birth = request('date_of_birth');
            $profile->gender = request('gender');

            if (request('status') == 'pending_activation')
                $user->notify(new Activation($user));   // notify user via email if the status is pending

            $user->profile()->save($profile);   // save the profile data in profile table

            return response()->json(['message' => 'User updated!']);

        } catch (\Exception $ex) {

            Log::error($ex->getMessage());

            return response()->json(['message' => 'Sorry, something went wrong!'], 422);
        }
    }
}
