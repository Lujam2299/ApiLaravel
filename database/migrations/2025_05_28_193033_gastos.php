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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('Monto', 10, 2);
            $table->date('Fecha');
            $table->time('Hora');
            $table->string('Evidencia');
            $table->enum('Tipo', ['Viaticos', 'Gasolina']);
            $table->string('user_name')->nullable();
            $table->decimal('Km', 10, 2)->nullable();
            $table->decimal('Gasolina_antes_carga', 10, 2)->nullable();
            $table->decimal('Gasolina_despues_carga', 10, 2)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
