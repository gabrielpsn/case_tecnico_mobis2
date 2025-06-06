<?php

namespace App\Repositories\Eloquent;

use App\Models\Veiculo;
use App\Repositories\Contracts\VeiculoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class VeiculoRepository implements VeiculoRepositoryInterface
{
    public function __construct(
        private readonly Veiculo $model
    ) {}

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Veiculo
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Veiculo
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Veiculo
    {
        $veiculo = $this->find($id);
        $veiculo->update($data);

        return $veiculo->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
