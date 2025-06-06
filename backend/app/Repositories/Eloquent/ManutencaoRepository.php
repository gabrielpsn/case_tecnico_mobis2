<?php

namespace App\Repositories\Eloquent;

use App\Models\Manutencao;
use App\Repositories\Contracts\ManutencaoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ManutencaoRepository implements ManutencaoRepositoryInterface
{
    public function all(): Collection
    {
        return Manutencao::all();
    }

    public function find(int $id): ?Manutencao
    {
        return Manutencao::findOrFail($id);
    }

    public function create(array $data): Manutencao
    {
        return Manutencao::create($data);
    }

    public function update(int $id, array $data): Manutencao
    {
        $manutencao = $this->find($id);
        $manutencao->update($data);

        return $manutencao->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) Manutencao::destroy($id);
    }

    public function findByVeiculo(int $veiculoId): Collection
    {
        return Manutencao::where('veiculo_id', $veiculoId)->get();
    }
}
