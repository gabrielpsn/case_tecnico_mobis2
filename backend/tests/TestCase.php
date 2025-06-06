<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Desativa as verificações de chave estrangeira
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpa as tabelas na ordem correta
        \App\Models\Manutencao::truncate();
        \App\Models\Veiculo::truncate();
        \App\Models\Motorista::truncate();

        // Reativa as verificações
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Executa as migrações
        $this->artisan('migrate:fresh');

        // Opcional: Executa os seeders
        $this->seed();
    }
}
