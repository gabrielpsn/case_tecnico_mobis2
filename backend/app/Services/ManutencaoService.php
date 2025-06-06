<?php

namespace App\Services;

use App\Repositories\Contracts\ManutencaoRepositoryInterface;

class ManutencaoService
{
    public function __construct(
        private ManutencaoRepositoryInterface $manutencaoRepository
    ) {}

    public function getAllManutencoes()
    {
        return $this->manutencaoRepository->all();
    }

    public function getManutencaoById(int $id)
    {
        return $this->manutencaoRepository->find($id);
    }

    public function createManutencao(array $data)
    {
        return $this->manutencaoRepository->create($data);
    }

    public function updateManutencao(int $id, array $data)
    {
        return $this->manutencaoRepository->update($id, $data);
    }

    public function deleteManutencao(int $id): bool
    {
        return $this->manutencaoRepository->delete($id);
    }

    public function getManutencoesByVeiculo(int $veiculoId)
    {
        return $this->manutencaoRepository->findByVeiculo($veiculoId);
    }
}
