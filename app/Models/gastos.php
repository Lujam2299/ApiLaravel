<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class gastos extends Model 
{
  protected $fillable = [
    'User_id',
    'Tipo',
    'Monto',
    'Fecha',
    'Hora',
    'Evidencia',
    'Km',
    'Gasolina_antes_carga',
    'Gasolina_despues_carga'
  ];
}
