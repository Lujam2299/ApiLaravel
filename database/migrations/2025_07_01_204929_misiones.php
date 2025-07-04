<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('misiones', function (Blueprint $table) {
            $table->id();
            $table->longText('agentes_id');
            $table->longText('nivel_amenaza');
            $table->string('tipo_servicio', 255);
            $table->string('ubicacion', 255);
            $table->string('fecha_inicio', 255);
            $table->string('fecha_fin', 255);
            $table->string('cliente', 255)->nullable();
            $table->string('pasajeros', 255)->nullable();
            $table->string('nombre_clave', 255)->nullable();
            $table->string('tipo_operacion', 255)->nullable();
            $table->integer('num_vehiculos')->nullable();
            $table->longText('tipo_vehiculos')->nullable();
            $table->string('armados', 255)->nullable();
            $table->longText('datos_hotel')->nullable();
            $table->longText('datos_aeropuerto')->nullable();
            $table->longText('datos_vuelo')->nullable();
            $table->longText('datos_hospital')->nullable();
            $table->longText('datos_embajada')->nullable();
            $table->longText('itinerarios')->nullable();
            $table->string('arch_mision', 255)->nullable();
            $table->string('estatus', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('misiones');
    }
};
