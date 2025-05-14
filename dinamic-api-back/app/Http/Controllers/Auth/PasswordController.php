<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    /**
     * Envía un enlace de recuperación al email del usuario
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debes introducir un correo válido.',
            'email.exists' => 'El correo introducido no existe.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $msg = __($status);
        $code = 400;
        if ($status === Password::RESET_LINK_SENT) {
            $msg = 'Se ha enviado el email de recuperación correctamente.';
            $code = 200;
        }
        
        if ($status === Password::INVALID_USER) {
            $msg = 'Usuario no encontrado.';
            $code = 404;
        }
        
        if ($status === Password::RESET_THROTTLED || $status === 'passwords.throttled') {
            $msg = 'Lo sentimos, se han realizado demasiados intentos. Vuelva a intentarlo más tarde.';
            $code = 429;
        }
        
        return response()->json(['msg' => $msg], $code);
    }

    /**
     * Realiza el reseteo de la contraseña
     */
    public function reset(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'token.required' => 'El token es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Debes introducir un correo válido.',
            'email.exists' => 'El correo introducido no existe.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'msg' => 'Error en la petición',
                'errors' => $validator->errors()
            ], 400);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['msg' => 'Contraseña actualizada correctamente.'])
            : response()->json(['msg' => 'No se pudo actualizar la contraseña.'], 400);
    }
}
