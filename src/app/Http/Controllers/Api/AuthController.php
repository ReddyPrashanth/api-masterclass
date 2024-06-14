<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\SignUpUserRequest;
use App\Mail\AccountVerification;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     * 
     * Authenticates the user and returns the user's API token.
     * @bodyParam email string User email address. Example: johndoe@example.com
     * @bodyParam password string User password.
     * @unauthenticated
     * @group Authentication
     * @response 200 {
     *      "data": {
     *          "token": "{YOUR_AUTH_KEY}"
     *       },
     *      "message": "Authenticated",
     *     "status": 200
     *  }
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);
        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth()
                )->plainTextToken
            ]
        );
    }

    /**
     * Logout
     * 
     * Signs out the user and destroy's the API token.
     * 
     * @group Authentication
     * @response 200 {}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }

    /**
     * Signup
     * 
     * Registers a user and sends account verification notification
     * 
     * @group Authentication
     * @response 200 {}
     */
    public function signup(SignUpUserRequest $request)
    {
        $user = User::create($request->only('name', 'email', 'password'));
        Mail::to($user)->queue((new AccountVerification($user->verificationUrl()))->onQueue('account'));
        return $this->ok(
            'Activation required. Please verify your account',
        );
    }

    /**
     * Account verification
     * 
     * Verifies user account
     * 
     * @group Authentication
     * @response 200 {}
     */
    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);
        if ($user->checkHash($hash)) {
            $user->verified();
            return $this->ok(
                'Account verification complete. Please try logging in.',
            );
        }
        return $this->error('Verification link expired. Please try again.', 410);
    }
}
