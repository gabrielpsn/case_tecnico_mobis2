<?php

namespace App\Services;

use App\Models\Motorista;
use App\Repositories\Contracts\MotoristaRepositoryInterface;

class MotoristaService
{
    protected $motoristaRepository;

    public function __construct(MotoristaRepositoryInterface $motoristaRepository)
    {
        $this->motoristaRepository = $motoristaRepository;
    }

    public function getAllMotoristas($perPage = 15, $filters = [])
    {
        $query = Motorista::query();

        // Aplicar filtros
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                    ->orWhere('cpf', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenação padrão
        $query->orderBy('nome');

        return $query->paginate($perPage);
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
