<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class AuthenticationController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * @throws ValidationException
     */
    public function login(Request $request): array
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $passwordGrantClient = Client::query()
            ->where('password_client', $request->password_client)
            ->first();

        $password = $request->password;
        $email = $request->email;
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (!$user)
            abort(404, 'Invalid details sent. Please review.');

        if (!Hash::check($password, $user->password))
            abort(404, 'Invalid details sent. Please review.');

        $token = $user->createToken($passwordGrantClient->name);
        return [
            'expires' => (int) Carbon::parse($token->token->expires_at)->fromNow(),
            'access_token' => $token->accessToken
        ];
    }

    public function logout()
    {
        auth()->guard('api')->user()->tokens()->delete();
    }
}
