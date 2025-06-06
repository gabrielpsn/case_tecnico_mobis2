<?php

namespace App\Services;

use App\Models\Veiculo;
use App\Repositories\Contracts\VeiculoRepositoryInterface;

class VeiculoService
{
    public function __construct(
        private readonly VeiculoRepositoryInterface $veiculoRepository
    ) {}

    public function getAllVeiculos()
    {
        return $this->veiculoRepository->all();
    }

    public function getVeiculo(int $id): Veiculo
    {
        return $this->veiculoRepository->find($id);
    }

    public function createVeiculo(array $data): Veiculo
    {
        return $this->veiculoRepository->create($data);
    }

    public function updateVeiculo(int $id, array $data): Veiculo
    {
        return $this->veiculoRepository->update($id, $data);
    }

    public function deleteVeiculo(int $id): bool
    {
        return $this->veiculoRepository->delete($id);
    }
}
