<?php

namespace App\Http\Controllers;

use App\Http\Requests\Veiculo\VeiculoRequest;
use App\Services\VeiculoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class VeiculoController extends Controller
{
    public function __construct(
        private readonly VeiculoService $veiculoService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->veiculoService->getAllVeiculos());
    }

    public function store(VeiculoRequest $request): JsonResponse
    {
        $veiculo = $this->veiculoService->createVeiculo($request->validated());

        return response()->json($veiculo, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->veiculoService->getVeiculo($id));
    }

    public function update(VeiculoRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->veiculoService->updateVeiculo($id, $request->validated())
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->veiculoService->deleteVeiculo($id);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
