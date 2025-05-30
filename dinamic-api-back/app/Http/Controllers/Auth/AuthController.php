<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debes introducir un correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $errorUser = false;

        if (!$user || !Hash::check($request->password, $user->password)) {
            $msg = 'Email o password incorrectos';
            $code = 401;
            $errorUser = true;
        }

        if (!$user->hasVerifiedEmail()) {
            $msg = 'Email no verificado.';
            $code = 403;
            $errorUser = true;
        }

        if ($errorUser) {
            return response()->json(['msg' => $msg], $code);
        }

        $token = $user->createToken('apiToken')->plainTextToken;

        return response()->json([
            'msg' => 'Usuario loggeado correctamente',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
            'token' => $token
        ], 200);
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|max:100|email|unique:users,email',
            'password' => 'required|string|confirmed'
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'email.email' => 'Debes introducir un correo válido.',
            'email.unique' => 'El correo ya existe.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $user->assignRole('user');
        $user->sendEmailVerificationNotification();

        $res = [
            'msg' => 'Usuario registrado correctamente, por favor, verifica el correo electrónico',
        ];
        return response()->json(($res), 201);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|between:2,100',
            'password' => 'sometimes|string|confirmed'
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $user->update(['name' => $request->name]);

        if ($request->password) {
            $user->update([
                'password' => bcrypt($request->password)
            ]);
        }

        $res = [
            'msg' => 'Usuario actualizado correctamente, por favor.',
            'user' => $user
        ];
        return response()->json(($res), 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return ['msg' => 'Se ha cerrado sesión correctamente.'];
    }

    public function getAccount()
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) {
            return response()->json(User::with('roles')->where('id', '=', auth()->user()->id)->first());
        }
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at
        ], 200);
    }

}
