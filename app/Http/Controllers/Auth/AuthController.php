<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\apiUser; 
use App\Models\Mision; 
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; 

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
          
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', Password::min(8)->letters()->numbers()],
                'telefono' => 'nullable|string|size:10|unique:users', 
                'rol' => 'nullable|in:interno,externo', 
                'punto' => 'nullable|string|max:255', 
            ], [
               
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

           
            $user = apiUser::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'telefono' => $validatedData['telefono'] ?? null, 
                'rol' => $validatedData['rol'] ?? 'interno',      
                'punto' => $validatedData['punto'] ?? null,    
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
                    'telefono' => $user->telefono, 
                    'rol' => $user->rol,          
                    'punto' => $user->punto,     
                ]
            ], 201);
        } catch (ValidationException $e) {
           
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Has cerrado sesión exitosamente.'], 200);
    }
}
