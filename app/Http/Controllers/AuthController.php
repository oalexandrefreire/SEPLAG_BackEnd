<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['token' => $user->createToken('auth_token', ['expires_in' => 300])->plainTextToken]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user->tokens()->delete();
        RefreshToken::where('user_id', $user->id)->delete();

        $accessToken = $user->createToken('auth_token');
        $accessToken->accessToken->expires_at = Carbon::now()->addMinutes(5);
        $accessToken->accessToken->save();

        $refreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(60),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return response()->json([
            'access_token' => $accessToken->plainTextToken,
            'access_expires_at' => $accessToken->accessToken->expires_at,
            'refresh_token' => $refreshToken->token,
            'refresh_expires_at' => $refreshToken->expires_at,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()?->delete();

        \App\Models\RefreshToken::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso. Tokens revogados.'
        ]);
    }


    public function refreshToken(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $refreshToken = RefreshToken::where('token', $request->refresh_token)->first();

        if (!$refreshToken || $refreshToken->isExpired()) {
            return response()->json(['message' => 'Refresh Token inválido ou expirado'], 401);
        }

        $user = $refreshToken->user;

        $refreshToken->delete();

        $newAccessToken = $user->createToken('auth_token');
        $newAccessToken->accessToken->expires_at = Carbon::now()->addMinutes(5);
        $newAccessToken->accessToken->save();

        $newRefreshToken = RefreshToken::create([
            'user_id' => $user->id,
            'token' => Str::random(60),
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        return response()->json([
            'access_token' => $newAccessToken->plainTextToken,
            'access_expires_at' => $newAccessToken->accessToken->expires_at,
            'refresh_token' => $newRefreshToken->token,
            'refresh_expires_at' => $newRefreshToken->expires_at,
        ]);
    }

}
