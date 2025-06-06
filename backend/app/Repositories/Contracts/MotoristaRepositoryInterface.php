<?php

namespace App\Repositories\Contracts;

use App\Models\Motorista;
use Illuminate\Database\Eloquent\Collection;

interface MotoristaRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Motorista;

    public function create(array $data): Motorista;

    public function update(int $id, array $data): Motorista;

    public function delete(int $id): bool;
}
