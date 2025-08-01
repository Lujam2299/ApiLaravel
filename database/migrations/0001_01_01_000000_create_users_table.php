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
       

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('sol_alta_id')->nullable()->after('id');
            // $table->unsignedBigInteger('sol_docs_id')->nullable()->after('sol_alta_id');
            $table->string('name');
            $table->string('email')->unique(); //Hacer nullable
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('rol')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->string('telefono')->unique()->nullable();
            $table->string('punto')->nullable();
            $table->string('estatus')->nullable();
            $table->string('empresa')->nullable();
            $table->string('num_empleado')->nullable();
            $table->rememberToken()->nullable();
            $table->softDeletes();
            $table->timestamps();

            // $table->foreign('sol_alta_id')
            //     ->references('id')
            //     ->on('solicitud_altas');
            // $table->foreign('sol_docs_id')
            //     ->references('id')
            //     ->on('documentacion_altas');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
