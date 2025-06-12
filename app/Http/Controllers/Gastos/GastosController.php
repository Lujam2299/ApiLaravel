<?php

namespace App\Http\Controllers\Gastos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\gastos;
use Illuminate\Validation\ValidationException;

class GastosController extends Controller
{
   public function guardarGastos(Request $request)
{
    try {
        // Validaciones base siempre y cuando el tipo sea viaticos
        $baseRules = [
            'User_id' => 'required|integer',
            'Monto' => 'required|numeric',
            'Fecha' => 'required|date',
            'Hora' => 'required|date_format:H:i',
            'Evidencia' => 'required|file',
            'Tipo' => 'required|in:Viaticos,Gasolina',
        ];

        // Condicionales extra cuando se elija Gasolina
        if ($request->input('Tipo') === 'Gasolina') {
            $baseRules += [
                'Km' => 'required|numeric',
                'Gasolina_antes_carga' => 'required|numeric',
                'Gasolina_despues_carga' => 'required|numeric',
            ];
        }

        // Valida los datos de la solicitud
        $validatedData = $request->validate($baseRules);

        // Guarda los datos válidos en la base de datos
        $gasto = gastos::create($validatedData);

        // Opción al manejar la carga de archivos
        if ($request->hasFile('Evidencia')) {
            $path = $request->file('Evidencia')->store('evidencias', 'public');
            $gasto->update(['Evidencia' => $path]);
        }

        return response()->json(['message' => 'Gasto guardado de manera exitosa', 'data' => $gasto], 201);
    } catch (ValidationException $e) {
        // Manejo de errores
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Se produjo un error al guardar los gastos.'], 500);
    }
}

}
