<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response(['msg' => 'Email o password incorrectos'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['msg' => 'Email no verificado.'], 403);
        }

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'msg' => 'Usuario loggeado correctamente',
            'user' => $user,
            'token' => $token
        ];

        return response()->json(($res), 200);
    }

    public function signUp(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|max:100|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        $user->assignRole('user');

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'msg' => 'Usuario registrado correctamente',
            'user' => $user,
            'token' => $token
        ];
        return response()->json(($res), 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        // Revoke all tokens...
        // $tokenId = $request->tokenId;

        // Revoke a specific token...
        // auth()->user()->tokens()->where('id', $tokenId)->delete();
        return ['msg' => 'Se ha cerrado sesión correctamente.'];
    }

    public function getAccount()
    {
        return response()->json(User::with('roles')->where('id', '=', auth()->user()->id)->first());
    }

}
