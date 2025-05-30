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
            $table->unsignedBigInteger('User_id');
            $table->enum('Tipo', ['Viaticos', 'Gasolina']);
            $table->decimal('Monto', 10, 2);
            $table->date('Fecha')->nullable();
            $table->time('Hora')->nullable();
            $table->string('Evidencia')->nullable();
            $table->decimal('Km', 10, 2)->nullable();
            $table->decimal('Gasolina_antes_carga', 10, 2)->nullable();
            $table->decimal('Gasolina_despues_carga', 10, 2)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('api_users')->onDelete('cascade');
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
