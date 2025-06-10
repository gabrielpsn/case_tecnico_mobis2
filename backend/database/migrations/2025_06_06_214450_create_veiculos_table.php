<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 7)->unique();
            $table->string('modelo', 100);
            $table->string('marca', 50);
            $table->year('ano');
            $table->enum('status', ['disponivel', 'em_uso', 'manutencao'])->default('disponivel');
            $table->decimal('quilometragem', 10, 2);
            $table->foreignId('motorista_id')->nullable()->constrained('motoristas')->onDelete('set null');
            $table->string('chassi', 17)->unique();
            $table->string('cor', 50);
            $table->string('observacoes', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculos');
    }
};
