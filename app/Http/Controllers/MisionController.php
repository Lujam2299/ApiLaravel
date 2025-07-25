<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mision;
use Illuminate\Support\Facades\Storage;
 use Illuminate\Support\Facades\Log;
 use Illuminate\Database\Eloquent\ModelNotFoundException;

class MisionController extends Controller
{

    public function misionesUsuario(Request $request)
    {
        try {
            $user = $request->user();

            $misiones = Mision::whereJsonContains('agentes_id', $user->id)
                ->whereIn('estatus', ['Activa', 'Pendiente'])
                ->select([
                    'id',
                    'nombre_clave',
                    'estatus',
                    'arch_mision',
                    'fecha_inicio',
                    'fecha_fin',
                    'updated_at'
                ])
                ->orderBy('estatus', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'misiones' => $misiones
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener misiones: ' . $e->getMessage()
            ], 500);
        }
    }


    public function archivoMision(Request $request, $misionId)
    {
        try {
            $user = $request->user();
            $mision = Mision::findOrFail($misionId);


            if (!in_array($user->id, $mision->agentes_id ?? [])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No estás asignado a esta misión'
                ], 403);
            }

            $response = [
                'success' => true,
                'mision' => [
                    'id' => $mision->id,
                    'nombre_clave' => $mision->nombre_clave,
                    'estatus' => $mision->estatus,
                    'fecha_inicio' => $mision->fecha_inicio,
                    'fecha_fin' => $mision->fecha_fin
                ],
                'estatus_actual' => $mision->estatus
            ];

            if (!empty($mision->arch_mision)) {
                $response['archivo'] = [
                    'nombre' => basename($mision->arch_mision),
                    'ruta' => $mision->arch_mision,
                    'tipo' => 'principal',
                    'fecha_actualizacion' => $mision->updated_at->format('Y-m-d H:i:s')
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de la misión: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function descargarArchivo(Request $request, $misionId){
    //     try {
    //         $user = $request->user();
    //         $mision = Mision::findOrFail($misionId);


    //         if (!in_array($user->id, $mision->agentes_id ?? [])) {
    //             abort(403, 'No estás asignado a esta misión');
    //         }


    //         $rutaArchivo = "misiones/{$mision->id}/documento_prueba.pdf";
    //         $rutaCompleta = storage_path('app/' . $rutaArchivo);

    //         if (!file_exists($rutaCompleta)) {
    //             abort(404, 'El archivo no existe');
    //         }

    //         return response()->download($rutaCompleta);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error al descargar: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
public function descargarArchivo(Request $request, $misionId) {
    try {
        
        Log::channel('descargas')->info("Inicio descarga archivo", [
            'user_id' => $request->user()->id,
            'mision_id' => $misionId,
            'ip' => $request->ip()
        ]);

        $user = $request->user();
        $mision = Mision::findOrFail($misionId);

        Log::debug("Datos completos de misión", ['mision' => $mision->toArray()]);

        
        if (!in_array($user->id, $mision->agentes_id ?? [])) {
            Log::warning("Acceso no autorizado", ['user_id' => $user->id]);
            abort(403, 'No estás asignado a esta misión');
        }

        
        $rutaArchivo = $mision->arch_mision; 
        
        if (empty($rutaArchivo)) {
            Log::error("Campo arch_mision vacío", ['mision_id' => $misionId]);
            abort(404, 'La misión no tiene archivo asociado');
        }

        
        $rutaRelativa = ltrim($rutaArchivo, '/');
        $rutaCompleta = storage_path($rutaRelativa);

        Log::debug("Rutas construidas", [
            'bd' => $rutaArchivo,
            'relativa' => $rutaRelativa,
            'completa' => $rutaCompleta
        ]);

        
        if (!file_exists($rutaCompleta)) {
            Log::error("Archivo no encontrado", [
                'ruta' => $rutaCompleta,
                'contenido_directorio' => scandir(dirname($rutaCompleta))
            ]);
            abort(404, 'El archivo no existe en el servidor');
        }

        
        Log::info("Iniciando descarga", ['ruta' => $rutaCompleta]);
        return response()->download($rutaCompleta, basename($rutaArchivo));

    } catch (ModelNotFoundException $e) {
        Log::error("Misión no encontrada", ['error' => $e->getMessage()]);
        abort(404, 'Misión no encontrada');
    } catch (\Exception $e) {
        Log::error("Error en descarga", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        abort(500, 'Error al procesar la descarga: ' . $e->getMessage());
    }
}
}
