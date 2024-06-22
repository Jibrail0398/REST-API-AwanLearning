<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Exception;

class userMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil Bearer Token
        $jwt = $request->bearerToken();

        // Kondisi jika token kosong
        if (is_null($jwt) || $jwt == '') {
            return response()->json(['message' => 'Token is missing'], 401);
        }

        try {
            // decrypt token
            $decoded = JWT::decode($jwt, new Key(env('JWT_SECRET_KEY'), 'HS256'));

            // Simpan informasi pengguna di dalam permintaan
            $request->user = (object) [
                'id' => $decoded->sub,
                'name' => $decoded->name,
                'email' => $decoded->email,
                'role' => $decoded->role,
            ];

            return $next($request);
        } catch (ExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json(['message' => 'Token signature is invalid'], 401);
        } catch (Exception $e) {
            return response()->json(['message' => 'Token is invalid: ' . $e->getMessage()], 401);
        }
    }
}
