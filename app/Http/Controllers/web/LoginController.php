<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Carbon\Carbon;
use App\Models\User;

class LoginController extends Controller {


    public function login(Request $request){
         // Validasi input
         $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        // Coba melakukan otentikasi
        if (Auth::attempt($validator->validated())) {
            // Buat payload JWT
            $user = Auth::user();
            if ($user->email_verified_at === null) {
                return response()->json(['message' => 'Akun belum diverifikasi'], 422);
            }
            $payload = [
                'sub' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'iat' => Carbon::now()->timestamp,
                'exp' => Carbon::now()->addHours(2)->timestamp 
            ];

            // Generate token JWT
            $jwt = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

            // Kirim respons JSON dengan token
            return response()->json([
                'message' => 'Token berhasil digenerate',
                'name' => $user->name,
                'token' => 'Bearer ' . $jwt
            ], 200);
        }

        // Jika otentikasi gagal
        return response()->json(['message' => 'Email atau password salah'], 422);
    }
    
}

