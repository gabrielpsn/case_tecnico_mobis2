<?php

namespace App\Repositories\Contracts;

use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Collection;

interface VeiculoRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Veiculo;

    public function create(array $data): Veiculo;

    public function update(int $id, array $data): Veiculo;

    public function delete(int $id): bool;
}
