<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mision;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\apiUser;
use PDF;
use Illuminate\Support\Str;

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
    public function descargarArchivo(Request $request, $misionId)
    {
        try {
            Log::info("Intentando descargar archivo para la misionId: $misionId");

            $user = $request->user();
            Log::info("Usuario ID: {$user->id} intentando acceder a la misionId: $misionId");

            $mision = Mision::findOrFail($misionId);
            Log::info("Misión encontrada: $misionId. Verificando permisos.");

            if (!in_array($user->id, $mision->agentes_id ?? [])) {
                Log::warning("Acceso denegado para usuario ID: {$user->id} en misionId: $misionId");
                abort(403, 'No estás asignado a esta misión');
            }

            Log::info("Permiso concedido para el usuario ID: {$user->id} en la misionId: $misionId. Generando PDF.");

            $pdf = PDF::loadView('misiones.pdf', [
                'mision' => $mision
            ]);

            $pdf->setPaper('A4', 'portrait');
            $nombreBase = Str::slug($mision->nombre_clave ?? 'reporte-mision');
            $nombreArchivo = "{$nombreBase}.pdf";

            return $pdf->download($nombreArchivo);
        } catch (\Exception $e) {
            Log::error("Error al generar el PDF para la misionId: $misionId. Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al generar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}
