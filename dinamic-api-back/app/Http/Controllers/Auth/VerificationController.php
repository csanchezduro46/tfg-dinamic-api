<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{

    public function verify($id, $hash, Request $request)
    {
        $user = User::findOrFail($id);

        if (!URL::hasValidSignature($request)) {
            return response()->json(['message' => 'El enlace no es válido o ha expirado.'], 403);
        }

        if (sha1($user->getEmailForVerification()) !== $hash) {
            return response()->json(['message' => 'Hash inválido.'], 403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(['message' => 'Correo verificado correctamente.']);
    }

    public function resend(Request $request)
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

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Este correo ya ha sido verificado.'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Correo de verificación reenviado.']);
    }
}
