<?php

namespace App\Http\Controllers;

use App\Models\EmailToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function Illuminate\Support\now;

class AuthController extends Controller
{
    public function signUp (Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validate->fails()) {
            return ResponseController::fails(
                'validation error', $validate->errors()->getMessageBag(), null, 422
            );
        }

        $data = $validate->validated();
        $hashedPassword = Hash::make($data['password']);

        $newUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
            'email_verified_at' => $data['email'] == 'aliefkhairulfadzli24@gmail.com' ? now() : null,
            'role' => $data['email'] == 'aliefkhairulfadzli24@gmail.com' ? 'supervisor' : 'user'
        ]);

        $newEmailToken = EmailToken::updateOrCreate([
            'user_id' => $newUser->id,
            'token' => Str::random(64),
            'expired_at' => now()->addDay()
        ]);

        return ResponseController::success(
            'sign-up successful', [
                'new_user' => $newUser,
                'email_verification_token' => $newEmailToken
            ]
        );
    }

    public function signIn (Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validate->fails()) {
            return ResponseController::fails(
                'validation error', $validate->errors()->getMessageBag(), null, 422
            );
        }

        $data = $validate->validated();
        $findUserByEmail = User::query()->where('email', $data['email'])->first();

        if (!$findUserByEmail || !Hash::check($validate->validated()['password'], $findUserByEmail['password'])) {
            return ResponseController::fails(
                'unauthorized', 'wrong password', null, 401
            );
        }

        $apiToken = $findUserByEmail->createToken('api_token')->plainTextToken;

        return ResponseController::success(
            'sign-in successful', [
                'user' => $findUserByEmail,
                'token' => $this->tokenResponse($apiToken)
            ]
        );
    }

    public function me (Request $request)
    {
        return ResponseController::success('get user successful', [
            'current_user' => $request->user()
        ], 200);
    }

    public function signOut (Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ]);
    }

    private function tokenResponse (String $apiToken)
    {
        return [
            'api_token' => $apiToken,
            'type' => 'Bearer'
        ];
    }
}
