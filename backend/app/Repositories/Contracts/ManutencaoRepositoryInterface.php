<?php

namespace App\Repositories\Contracts;

use App\Models\Manutencao;
use Illuminate\Database\Eloquent\Collection;

interface ManutencaoRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Manutencao;

    public function create(array $data): Manutencao;

    public function update(int $id, array $data): Manutencao;

    public function delete(int $id): bool;

    public function findByVeiculo(int $veiculoId): Collection;
}
