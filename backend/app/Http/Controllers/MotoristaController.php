<?php

namespace App\Http\Controllers;

use App\Http\Requests\Motorista\MotoristaRequest;
use App\Services\MotoristaService;
use Illuminate\Http\JsonResponse;

class MotoristaController extends Controller
{
    protected $motoristaService;

    public function __construct(MotoristaService $motoristaService)
    {
        $this->motoristaService = $motoristaService;
    }

    public function index(): JsonResponse
    {
        $perPage = request()->input('per_page', 15);
        $filters = request()->only(['search']);

        $motoristas = $this->motoristaService->getAllMotoristas($perPage, $filters);

        return response()->json([
            'data' => $motoristas->items(),
            'meta' => [
                'current_page' => $motoristas->currentPage(),
                'last_page' => $motoristas->lastPage(),
                'per_page' => $motoristas->perPage(),
                'total' => $motoristas->total(),
            ],
        ]);
    }

    public function store(MotoristaRequest $request): JsonResponse
    {
        $motorista = $this->motoristaService->createMotorista($request->validated());

        return response()->json($motorista, 201);
    }

    public function show(int $id): JsonResponse
    {
        $motorista = $this->motoristaService->getMotorista($id);

        return response()->json($motorista);
    }

    public function update(MotoristaRequest $request, int $id): JsonResponse
    {
        $motorista = $this->motoristaService->updateMotorista($id, $request->validated());

        return response()->json($motorista);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->motoristaService->deleteMotorista($id);

        return response()->json(null, 204);
    }
}
