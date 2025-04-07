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
                'email' => $newUser->email
            ]
        ], 200)->cookie('jwt_token', $token, 60, '/', null, false, true);
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        if(Auth::attempt(["username" => $request->username, "password" => $request->password])){
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;

            return response()->json([
                'status' => 200,
                'message' => 'Login successful',
                'data' => [
                    'username' => $user->username,
                    'email' => $user->email
                ]
            ], 200)->cookie('jwt_token', $token, 60, '/', null, false, true);

        }

        return response()->json([
            'status' => 400,
            'message' => 'Login failed',
            'data' => null
        ], 400);
    }

    public function logout(Request $request){
        $token = $request->cookie('jwt_token');

        if (!$token) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized - No active session'
            ], 401);
        }

        $request->user()->token()->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Logout successful'
        ], 200)->cookie('jwt_token', '', -1);
    }
}
