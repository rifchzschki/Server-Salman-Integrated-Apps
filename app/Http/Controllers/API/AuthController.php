<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/'
            ],
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Registration failed',
                'errors' => $validator->errors()->all()
            ], 400);
        }
        $newUser = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $newUser->createToken('MyApp')->accessToken;

        return response()->json([
            'status' => 200,
            'message' => 'Registration successful',
            'data' => [
                'email' => $newUser->email,
                'access_token' => $token
            ]
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user || !\Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Username or password is incorrect',
                'data' => null
            ], 400);
        }

        $token = $user->createToken('MyApp')->accessToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'data' => [
                'email' => $user->email,
                'access_token' => $token
            ]
        ]);
    }
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'first_name' => $googleUser->user['given_name'] ?? '',
                    'last_name' => $googleUser->user['family_name'] ?? '',
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                ]
            );

            $token = $user->createToken('MyApp')->accessToken;
            $clientUrl = config('services.client.url');
            return redirect(`{$clientUrl}/auth/callback?token={$token}&email={$user->email}`);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Google login failed', 'details' => $e->getMessage()], 500);
        }
    }


    public function logout(Request $request)
    {
        auth()->logout();

        return response()->json([
            'status' => 200,
            'message' => 'Logout successful'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message' => 'User profile fetched successfully',
            'data' => [
                'id' => Auth::user()->id,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'role' => Auth::user()->role,
                'email' => Auth::user()->email,
            ]
        ]);
    }
}
