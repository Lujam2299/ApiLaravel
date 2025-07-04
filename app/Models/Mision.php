<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mision extends Model
{
    protected $table = 'misiones';

    protected $fillable = [
        'agentes_id',
        'nivel_amenaza',
        'tipo_servicio',
        'ubicacion',
        'fecha_inicio',
        'fecha_fin',
        'cliente',
        'pasajeros',
        'nombre_clave',
        'tipo_operacion',
        'num_vehiculos',
        'tipo_vehiculos',
        'armados',
        'datos_hotel',
        'datos_aeropuerto',
        'datos_vuelo',
        'datos_hospital',
        'datos_embajada',
        'itinerarios',
        'arch_mision',
        'estatus',
    ];
    protected $casts = [
        'itinerarios' => 'array',
        'agentes_id' => 'array',
        'tipo_vehiculos' => 'array',
        'datos_hotel' => 'array',
        'datos_aeropuerto' => 'array',
        'datos_vuelo' => 'array',
        'datos_hospital' => 'array',
        'datos_embajada' => 'array'
    ];


    public function agregarItinerario($user_id, $evento)
    {
        $itinerarios = $this->itinerarios ?? [];


        //busca si el usurio ya tiene el evento en su itinerario
        $index  = $this->buscarIndiceUsuario($user_id, $itinerarios);
        if ($index === false) {
            //agrega el evento al usuario existente
            $itinerarios[$index]['eventos'][] = $evento;
        } else {
            //crear el nueve registro al usuario
            $itinerarios[] = [
                'user_id' => $user_id,
                'eventos' => [$evento]
            ];
        }
        $this->itinerarios = $itinerarios;
    }

    private function buscarIndiceUsuario($user_id, $itinerarios)
    {
        foreach ($itinerarios as $index => $itinerario) {
            if ($itinerario['user_id'] == $user_id) {
                return $index;
            }
        }
        return false;
    }
}
