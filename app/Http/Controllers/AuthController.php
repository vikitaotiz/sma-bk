<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Users\UserResource;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request) {
        $userExist = User::where('name', $request->name)
            ->where('email', $request->email)
            ->first();

        if($userExist) return response([
            'status' => 'error',
            'message' => 'User already exists, try a different one.'
        ]);

        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'uuid' => Str::uuid()->toString(),
            // 'creator_id' => User::first()->id,
            // 'provider' => "email_password",
            'password' => bcrypt($fields['password'])
        ]);

        $user->roles()->attach([Role::where('name', 'Normal User')->first()->id]);

        if(!$user) return response([
            'status' => 'error',
            'message' => 'Registered failed.',
        ]);

        $token = $user->createToken('token')->plainTextToken;
        $user = new UserResource($user);

        $response = [
            'status' => 'success',
            'message' => 'User registered successfully.',
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $user = null;
        if(Auth::attempt($request->only('email', 'password'))) $user = Auth::user();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'status' => 'error',
                'message' => 'User does not exist, invalid credentials. Contact Admin'
            ], 401);
        }

        if($user && !$user->active) {
            return response([
                'status' => 'error',
                'message' => 'User has been disabled. Contact admin'
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;
        $user = new UserResource($user);

        $user_auth = UserAuth::where('user_id', $user->id)->first();
        if($user_auth){
            $user_auth->update(['authenticated' => true]);
        } else {
            UserAuth::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'authenticated' => true
            ]);
        }

        $response = [
            'status' => 'success',
            'message' => 'User logged in successfully.',
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->tokens()->delete();

        UserAuth::where('user_id', $user->id)->first()->update(['authenticated' => false]);

        return [
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ];
    }

    public function refresh(Request $request)
    {
        $user = auth()->user();

        if($user){
            $user->tokens()->delete();
            $token = $user->createToken('token')->plainTextToken;

            return response([
                'status' => 'success',
                'message' => 'Token refreshed successfully.',
                'user' => $user,
                'token' => $token
            ]);

        } else {
            return response([
                'status' => 'error',
                'message' => 'Token refresh failed. User not found'
            ]);
        }

    }
}
