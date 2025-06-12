<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\apiUser;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;




class AuthController extends Controller
{


    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            ], [
                'email.required' => 'El correo es requerido.',
                'email.email' => 'Por favor proporcione un correo válido.',
                'password.required' => 'Contraseña es requerida.',
                'password.min' => 'La contraseña debe contener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.',
                'password.letters' => 'La contraseña debe contener al menos 1 letra.',
                'password.mixedCase' => 'La contraseña debe contener al menos 1 letra mayúscula.',
                'password.numbers' => 'La contraseña debe contener al menos 1 número.',
                'password.symbols' => 'La contraseña debe contener al menos 1 símbolo.',
            ]);

            $user = apiUser::where('email', $validatedData['email'])->first();

            if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                return response()->json(['message' => 'Credenciales inválidas'], 401);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Ingreso exitoso',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:api_users',
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'telefono' => 'required|string|size:10',
            ], [
                'email.required' => 'El correo es requerido.',
                'password.letters' => 'La contraseña debe contener al menos 1 letra.',
                'password.mixedCase' => 'La contraseña debe contener al menos 1 letra mayúscula.',
                'password.numbers' => 'La contraseña debe contener al menos 1 número.',
                'password.symbols' => 'La contraseña debe contener al menos 1 símbolo.',
                'email.unique' => 'Este correo ya está registrado.',
                'email.email' => 'Por favor proporcione un correo válido.',
                'password.required' => 'Contraseña es requerida.',
                'password.min' => 'La contraseña debe contener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.',
                'telefono.size' => 'El teléfono debe contener al 10 dígitos.',
            ]);


            $user = apiUser::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'telefono' => $validatedData['telefono'],
                'remember_token' => Str::random(80),
                'email_verified_at' => now()
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Registro exitoso',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Has cerrado sesión exitosamente.'],200);
    } 
}
