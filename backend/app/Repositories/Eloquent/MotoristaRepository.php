<?php

namespace App\Repositories\Eloquent;

use App\Models\Motorista;
use App\Repositories\Contracts\MotoristaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MotoristaRepository implements MotoristaRepositoryInterface
{
    protected $model;

    public function __construct(Motorista $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Motorista
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Motorista
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Motorista
    {
        $motorista = $this->find($id);
        $motorista->update($data);

        return $motorista->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
