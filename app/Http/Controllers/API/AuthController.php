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
        //TODO 2: BUAT REGEX PASSWORD
        //TODO 3 (DONE): TAMBAH KOLOM FIRSTNAME DAN LASTNAME KE DATABASE - DONE
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
        $existingUser = User::where('username',$request->name)->orWhere('email', $request->email)->first();

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

        $response = [];
        $response['status'] = 200;
        $response['message'] = 'Registration successful';
        $data = [];
        $data['user'] = $newUser->username;
        $data['email'] = $newUser->email;
        $data['token'] = $newUser->createToken('MyApp')->accessToken;
        $response['data'] = $data;

        return response()->json($response, 200);
    }

    public function login(Request $request){
        if(Auth::attempt(["username" => $request->username, "password" => $request->password])){
            $user = Auth::user();
            $response = [];
            $response['status'] = 200;
            $response['message'] = 'Login successful';
            $data = [];
            $data['username'] = $user->username;
            $data['email'] = $user->email;
            $data['token'] = $user->createToken('MyApp')->accessToken;
            $response['data'] = $data;

            return response()->json($response, 200);
        }

        return response()->json([
            'status' => 400,
            'message' => 'Login failed',
            'data' => null
        ], 400);
    }
}
