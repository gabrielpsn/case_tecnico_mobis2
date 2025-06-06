<?php

namespace App\Services;

use App\Repositories\Contracts\MotoristaRepositoryInterface;

class MotoristaService
{
    protected $motoristaRepository;

    public function __construct(MotoristaRepositoryInterface $motoristaRepository)
    {
        $this->motoristaRepository = $motoristaRepository;
    }

    public function getAllMotoristas()
    {
        return $this->motoristaRepository->all();
    }

    public function getMotorista(int $id)
    {
        return $this->motoristaRepository->find($id);
    }

    public function createMotorista(array $data)
    {
        return $this->motoristaRepository->create($data);
    }

    public function updateMotorista(int $id, array $data)
    {
        return $this->motoristaRepository->update($id, $data);
    }

    public function deleteMotorista(int $id): bool
    {
        return $this->motoristaRepository->delete($id);
    }
}
