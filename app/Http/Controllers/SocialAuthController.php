<?php

namespace App\Http\Controllers;

use App\Models\Profile\Profile;
use App\Models\User\User;
use Illuminate\Http\Request;
use JWTAuth;
use Socialite;

class SocialAuthController extends Controller
{
    /**
     * @param string $provider
     *
     * @return $this
     */
    public function providerRedirect($provider = '')
    {
        if (!in_array($provider, ['facebook', 'twitter', 'github'])) {
            return redirect('/login')->withErrors('This is not a valid link.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param string $provider
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function providerRedirectCallback($provider = '')
    {
        try {
            $user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect('/auth/social');
        }

        $user_exists = User::whereEmail($user->email)->first();

        if ($user_exists) {
            $token = JWTAuth::fromUser($user_exists);
        } else {
            $new_user = new User();
            $new_user->email = $user->email;
            $new_user->provider = $provider;
            $new_user->provider_unique_id = $user->id;
            $new_user->status = 'activated';
            $new_user->activation_token = generateUuid();
            $new_user->save();
            $name = explode(' ', $user->name);
            $profile = new Profile();
            $profile->user()->associate($new_user);
            $profile->first_name = array_key_exists(0, $name) ? $name[0] : 'John';
            $profile->last_name = array_key_exists(1, $name) ? $name[1] : 'Doe';
            $profile->save();
            $token = JWTAuth::fromUser($new_user);
        }

        \Cache::put('jwt_token', $token, 1);

        return redirect('/auth/social');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getToken()
    {
        if (!\Cache::has('jwt_token')) {
            return response()->json(['message' => 'Invalid request.'], 422);
        }

        $token = \Cache::get('jwt_token');
        \Cache::forget('jwt_token');

        return response()->json(['message' => 'You are successfully logged in!', 'token' => $token]);
    }
}
