<?php

namespace App\Http\Controllers\Turnos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\turno;
use Illuminate\Validation\ValidationException;

class TurnosController extends Controller
{
    public function guardarTurno(Request $request)
    {
        $baseRules = [
            'User_id' => 'required|integer',
            'Nombre_elemento' => 'required|string',
            'Punto' => 'required|string',
            'Placas_unidad' => 'required|string',
            'Tipo' => 'required|in:Entrada,Salida',
        ];

        if ($request->input('Tipo') === 'Entrada') {
            $baseRules += [
                'Hora_inicio' => 'required|date_format:H:i',
                'Km_inicio'  => 'required|numeric',
                'Rayas_gasolina_inicio' => 'required|numeric',
                'Evidencia_inicio' => 'required|file',
            ];
        } elseif ($request->input('Tipo') === 'Salida') {
            $baseRules += [
                'Hora_final' => 'required|date_format:H:i',
                'Km_final' => 'required|numeric',
                'Rayas_gasolina_final' => 'required|numeric',
                'Evidencia_final' => 'required|file',
            ];
        }

        try {

            $validatedData = $request->validate($baseRules);


            $turno = turno::create($validatedData);

            return response()->json(['message' => 'Turno guardado exitosamente', 'data' => $turno], 201);
        } catch (ValidationException $e) {

            return response()->json(['message' => 'Validación fallida', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            return response()->json(['message' => 'Error al guardar el turno', 'error' => $e->getMessage()], 500);
        }
    }
}
