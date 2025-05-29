<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class gastos extends Model
{

  use HasFactory;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'gastos';

  /**
   * The primary key associated with the table.
   *
   * @var string
   */
  protected $primaryKey = 'id';

  /**
   * Indicates if the model should be timestamped.
   *
   * @var bool
   */
  public $timestamps = true;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
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
  protected $casts = [
    'Monto' => 'decimal:2',
    'Fecha' => 'date',
    'Hora' => 'time',
    'Km' => 'decimal:2',
    'Gasolina_antes_carga' => 'decimal:2',
    'Gasolina_despues_carga' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];
}
