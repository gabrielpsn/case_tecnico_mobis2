<?php

namespace App\Http\Controllers;

use App\Http\Requests\Manutencao\ManutencaoRequest;
use App\Services\ManutencaoService;
use Illuminate\Http\JsonResponse;

class ManutencaoController extends Controller
{
    public function __construct(private ManutencaoService $manutencaoService) {}

    public function index(): JsonResponse
    {
        $manutencoes = $this->manutencaoService->getAllManutencoes();

        return response()->json($manutencoes);
    }

    public function store(ManutencaoRequest $request): JsonResponse
    {
        $manutencao = $this->manutencaoService->createManutencao($request->validated());

        return response()->json($manutencao, 201);
    }

    public function show(int $id): JsonResponse
    {
        $manutencao = $this->manutencaoService->getManutencaoById($id);

        return response()->json($manutencao);
    }

    public function update(ManutencaoRequest $request, int $id): JsonResponse
    {
        $manutencao = $this->manutencaoService->updateManutencao($id, $request->validated());

        return response()->json($manutencao);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->manutencaoService->deleteManutencao($id);

        return response()->json(null, 204);
    }

    public function porVeiculo(int $veiculoId): JsonResponse
    {
        $manutencoes = $this->manutencaoService->getManutencoesByVeiculo($veiculoId);

        return response()->json($manutencoes);
    }
}
