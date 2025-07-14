<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\apiUser; // Asegúrate de que este es el modelo de usuario correcto
use App\Models\Mision; // ¡IMPORTANTE! Importa el modelo Mision
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; // Asegúrate de importar Auth para Auth::user() si lo usas

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => ['required', Password::min(8)->letters()->numbers()],
            ], [
                'email.required' => 'El correo es requerido.',
                'email.email' => 'Por favor proporcione un correo válido.',
                'password.required' => 'Contraseña es requerida.',
                'password.min' => 'La contraseña debe contener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.',
                'password.letters' => 'La contraseña debe contener al menos 1 letra.',
                'password.numbers' => 'La contraseña debe contener al menos 1 número.',
            ]);

            $user = apiUser::where('email', $validatedData['email'])->first();

            if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                return response()->json(['message' => 'Credenciales inválidas'], 401);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            // --- LÓGICA AGREGADA: ENCONTRAR LA MISIÓN ACTIVA DEL USUARIO ---
            // Busca la misión activa a la que el usuario está asignado.
            // Se asume que 'agentes_id' en la tabla 'misiones' es un campo JSON.
            $misionActiva = Mision::where('estatus', 'Activa')
                                  ->whereJsonContains('agentes_id', $user->id) // Verifica si el user_id está en agentes_id
                                  ->first(); // Obtiene la primera misión activa encontrada

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [ // Añade esta sección con los datos del usuario
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'punto' => $user->punto ?? null, // Asegúrate de que 'punto' exista en tu modelo apiUser
                    'mision_id_activa' => $misionActiva ? $misionActiva->id : null, // Envía la ID de la misión activa
                ],
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
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', Password::min(8)->letters()->numbers()],
                'telefono' => 'nullable|string|size:10|unique:users', // 'nullable' and 'unique' as per your migration
                'rol' => 'nullable|in:interno,externo', // 'nullable' and restricted to 'interno' or 'externo'
                'punto' => 'nullable|string|max:255', // 'nullable' and string
            ], [
                // Custom validation messages for better user feedback
                'email.required' => 'El correo es requerido.',
                'password.letters' => 'La contraseña debe contener al menos 1 letra.',
                'password.numbers' => 'La contraseña debe contener al menos 1 número.',
                'email.unique' => 'Este correo ya está registrado.',
                'email.email' => 'Por favor proporcione un correo válido.',
                'password.required' => 'Contraseña es requerida.',
                'password.min' => 'La contraseña debe contener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y símbolos.',
                'telefono.size' => 'El teléfono debe contener 10 dígitos si se provee.',
                'telefono.unique' => 'Este teléfono ya está registrado.',
                'rol.in' => 'El rol debe ser "interno" o "externo".',
            ]);

            // Create the new user using the validated data
            $user = apiUser::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'telefono' => $validatedData['telefono'] ?? null, // Assign null if not provided in request
                'rol' => $validatedData['rol'] ?? 'interno',      // Assign validated role, or 'interno' as default
                'punto' => $validatedData['punto'] ?? null,      // Assign null if not provided
                'remember_token' => Str::random(80), // Consider removing if only using Sanctum tokens
                'email_verified_at' => now() // Typically set to null and filled upon email verification
            ]);

            // Create a Sanctum authentication token for the new user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return a successful JSON response with the access token and user data
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Registro exitoso',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'telefono' => $user->telefono, // Include telefono in the response
                    'rol' => $user->rol,          // Include rol in the response
                    'punto' => $user->punto,      // Include punto in the response
                ]
            ], 201);
        } catch (ValidationException $e) {
            // Catch and respond with validation errors
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Catch and respond with generic internal server errors
            // In a production environment, you would log $e->getMessage() for debugging.
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Has cerrado sesión exitosamente.'], 200);
    }
}
