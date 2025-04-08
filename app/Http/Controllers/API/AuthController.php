<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        //TODO REGISTER
        //TODO 1: BUAT FITUR VERIFIKASI EMAIL
        //TODO 2 (DONE): BUAT REGEX PASSWORD - DONE
        //TODO 3 (DONE): TAMBAH KOLOM FIRSTNAME DAN LASTNAME KE DATABASE - DONE
        $validator = Validator::make($request->all(), [
            'username' => 'required',
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
        $existingUser = User::where('username',$request->username)->orWhere('email', $request->email)->first();

        if($existingUser){
            return response()->json([
                'status' => 400,
                'message' => 'Registration failed',
                'errors' => 'User with this username or email already exists'
            ], 400);
        }
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'message' => 'Registration failed',
                'errors' => $validator->errors()->all()
            ], 400);
        }



        $newUser = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            "password" => bcrypt($request->password)
        ]);

        $token = $newUser->createToken('MyApp')->accessToken;
        return response()->json([
            'status' => 200,
            'message' => 'Registration successful',
            'data' => [
                'user' => $newUser->username,
                'email' => $newUser->email,
                'access_token' => $token
            ]
        ], 200);
    }

    public function login(Request $request){
        $request->headers->set('Accept', 'application/json');
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Login failed',
                'data' => null
            ], 400);
        }

        $token = $user->createToken('MyApp')->accessToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'data' => [
                'username' => $user->username,
                'email' => $user->email,
                'access_token' => $token
            ]
        ], 200);
    }

    public function logout(Request $request){
        $user = $request->user();

        if ($user) {
            $user->token()->revoke();

            return response()->json([
                'status' => 200,
                'message' => 'Logout successful'
            ], 200);
        }

        return response()->json([
            'status' => 401,
            'message' => 'Unauthorized - No active session'
        ], 401);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => 200,
            'message' => 'User profile fetched successfully',
            'data' => [
                'username' => Auth::user()->username,
                'first_name' => Auth::user()->first_name,
                'last_name' => Auth::user()->last_name,
                'email' => Auth::user()->email,
            ]
        ]);
    }

}
