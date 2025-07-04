<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mision; // ¡MUY IMPORTANTE! Asegúrate de que esta línea esté presente para importar tu modelo Mision.

class MisionController extends Controller
{
    public function archivosMision(Request $request, $misionId)
    {
        try {
            // Obtiene el usuario autenticado de la solicitud.
            // Esto asume que tienes un middleware de autenticación aplicado a la ruta.
            $user = $request->user();

            // Encuentra la misión por su ID, o lanza una excepción 404 si no se encuentra.
            $mision = Mision::findOrFail($misionId);
            
            // Decodifica la cadena JSON 'agentes_id' a un array.
            // Usa el operador de fusión nula (??) para que sea un array vacío si es nulo.
            $agentes = json_decode($mision->agentes_id, true) ?? [];
            
            // Verifica si el ID del usuario autenticado está en la lista de agentes asignados.
            if (!in_array($user->id, $agentes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No estás asignado a esta misión'
                ], 403); // Devuelve 403 Prohibido si no está asignado.
            }
            
            // Filtra por el estado de la misión: solo se permiten misiones 'Activa' o 'Pendiente'.
            // CORRECCIÓN CLAVE: Se agregó el paréntesis de cierre ')' después del array.
            if (!in_array($mision->estatus, ['Activa', 'Pendiente'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La misión no está activa ni pendiente'
                ], 400); // Devuelve 400 Solicitud Incorrecta si el estado no es válido.
            }
            
            // Verifica si el campo 'arch_mision' (ruta del archivo) está vacío.
            if (empty($mision->arch_mision)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta misión no tiene archivo asociado'
                ], 404); // Devuelve 404 No Encontrado si no hay archivo asociado.
            }
            
            // Si todas las verificaciones pasan, devuelve los detalles del archivo en una respuesta JSON.
            return response()->json([
                'success' => true,
                'archivo' => [
                    'nombre' => basename($mision->arch_mision), // Obtiene el nombre base del archivo.
                    'ruta' => $mision->arch_mision,
                    'estatus' => $mision->estatus,
                    'mision_id' => $mision->id,
                    'nombre_clave' => $mision->nombre_clave
                ]
            ]);
            
        } catch (\Exception $e) {
            // Captura cualquier excepción general y devuelve un error 500 de Servidor Interno.
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function descargarArchivo(Request $request, $misionId)
    {
        try {
            // Obtiene el usuario autenticado.
            $user = $request->user();
            // Encuentra la misión.
            $mision = Mision::findOrFail($misionId);
            
            // Verifica la asignación del usuario a la misión.
            $agentes = json_decode($mision->agentes_id, true) ?? [];
            if (!in_array($user->id, $agentes)) {
                abort(403, 'No estás asignado a esta misión'); // Aborta con 403 si no está asignado.
            }
            
            // Verifica el estado de la misión y la existencia del archivo.
            if (!in_array($mision->estatus, ['Activa', 'Pendiente']) || empty($mision->arch_mision)) {
                abort(404, 'Archivo no disponible'); // Aborta con 404 si el archivo no está disponible.
            }
            
            // Construye la ruta completa al archivo en el almacenamiento.
            $rutaCompleta = storage_path('app/' . $mision->arch_mision);
            
            // Verifica si el archivo realmente existe en el servidor.
            if (!file_exists($rutaCompleta)) {
                abort(404, 'El archivo no existe en el servidor'); // Aborta con 404 si el archivo no existe.
            }
            
            // Devuelve el archivo como una respuesta de descarga.
            return response()->download($rutaCompleta);
            
        } catch (\Exception $e) {
            // Captura cualquier excepción general durante la descarga y devuelve un error 500.
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}