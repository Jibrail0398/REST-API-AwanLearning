<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $messages = [
            'email.unique' => 'Email sudah digunakan.'
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ], $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $defaultRoleId = 'user';

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $defaultRoleId,
            ]);

            event(new Registered($user));

            return response()->json(['message' => 'User registered successfully. Please check your email for verification link.'], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'Failed to register user.'], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        $user = User::findOrFail($request->id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect('/verifysucess')->with('message', 'Email verified successfully.');
    }
    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent.']);
    }
}