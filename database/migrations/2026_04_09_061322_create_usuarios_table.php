<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            $table->string('apellido', 60);
            $table->string('email', 30)->unique();
            $table->string('password', 255);
            $table->string('telefono', 10)->nullable();
            $table->boolean('admin')->default(false);
            $table->boolean('confirmado')->default(false);
            $table->string('token', 15)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
