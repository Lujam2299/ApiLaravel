<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mision;

class MisionItinerarioController extends Controller
{
    //Agregar evento al itinerario

public function store(Request $request, $mision_id)
{
    $request->validate([
        'user_id' => 'required|integer',
        'fecha' => 'required|date',
        'hora' => 'required|date_format:H:i',
        'descripcion' => 'required|string|max:255',
        'ubicacion' => 'nullable|string|max:255'
    ]);

    $mision = Mision::findOrFail($mision_id);
    $user = $request->user();

    // Verificar que la misión esté activa (comparación insensible a mayúsculas)
    if (strtolower($mision->estatus) !== 'activa') {
        return response()->json(['message' => 'Misión no activa'], 400);
    }

    // Verificar que el usuario esté asignado a la misión
    $agents = is_array($mision->agentes_id) ? $mision->agentes_id : json_decode($mision->agentes_id, true) ?? [];
    
    if (!in_array($request->user_id, $agents)) {
        return response()->json(['message' => 'Usuario no asignado a la misión'], 403);
    }

    // Crear el evento
    $evento = [
        'user_id' => $request->user_id,
        'fecha' => $request->fecha,
        'hora' => $request->hora,
        'descripcion' => $request->descripcion,
        'ubicacion' => $request->ubicacion,
        'created_at' => now()->toDateTimeString()
    ];

    // Agregar al itinerario
    $itinerarios = is_array($mision->itinerarios) ? $mision->itinerarios : json_decode($mision->itinerarios, true) ?? [];
    
    $userIndex = array_search($request->user_id, array_column($itinerarios, 'user_id'));
    
    if ($userIndex !== false) {
        $itinerarios[$userIndex]['eventos'][] = $evento;
    } else {
        $itinerarios[] = [
            'user_id' => $request->user_id,
            'eventos' => [$evento]
        ];
    }

    $mision->itinerarios = $itinerarios;
    $mision->save();

    return response()->json([
        'message' => 'Evento agregado al itinerario',
        'itinerarios' => $itinerarios
    ]);
}

    // Obtener itinerarios de un usuario específico
    public function show($mision_id, $user_id)
    {
       $mision = Mision::findOrFail($mision_id);

        // 1. Verificar que la misión esté activa
        if (strtolower($mision->estatus) !== 'activa') {
            return response()->json(['message' => 'Misión no activa'], 400);
        }

        // 2. Verificar que el usuario actual esté asignado a la misión
        // (Opcional pero recomendado para seguridad: usa el usuario autenticado para la verificación)
        $currentUser = auth()->user(); // Obtener el usuario autenticado
        if (!$currentUser || $currentUser->id != $user_id) { // Asegura que el user_id de la URL sea el del usuario logeado
             return response()->json(['message' => 'Acceso no autorizado al itinerario de este usuario.'], 403);
        }

        $agents = is_array($mision->agentes_id) ? $mision->agentes_id : json_decode($mision->agentes_id, true) ?? [];
        if (!in_array($user_id, $agents)) {
            return response()->json(['message' => 'Usuario no asignado a la misión.'], 403);
        }

        $itinerarios = is_array($mision->itinerarios) ? $mision->itinerarios : json_decode($mision->itinerarios, true) ?? [];

        $userItinerarios = [];
        foreach ($itinerarios as $itinerario) {
            if (isset($itinerario['user_id']) && $itinerario['user_id'] == $user_id) {
                // Opcional: ordenar eventos por fecha y hora si no lo están
                $eventos = collect($itinerario['eventos'])->sortBy(function ($evento) {
                    return $evento['fecha'] . ' ' . $evento['hora'];
                })->values()->all();
                $userItinerarios = $eventos;
                break;
            }
        }

        return response()->json([
            'user_id' => $user_id,
            'eventos' => $userItinerarios
        ]);
    }

    // Obtener todos los itinerarios de la misión
    public function index($mision_id)
    {
        $mision = Mision::findOrFail($mision_id);
        return response()->json($mision->itinerarios ?? []);
    }

    
}
