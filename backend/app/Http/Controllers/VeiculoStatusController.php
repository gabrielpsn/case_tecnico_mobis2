<?php

namespace App\Http\Controllers;

use App\Models\VeiculoStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VeiculoStatusController extends Controller
{
    /**
     * Armazena um novo status de veículo
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'veiculoId' => 'required|string',
            'timestamp' => 'required|date',
            'status' => 'required|array',
            'status.rpm' => 'required|numeric',
            'status.temperatura' => 'required|numeric',
            'status.combustivel' => 'required|numeric|min:0|max:100',
            'status.checkEngine' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = VeiculoStatus::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $status,
        ], 201);
    }

    /**
     * Lista os status de um veículo
     *
     * @param  string  $veiculoId
     * @return \Illuminate\Http\Response
     */
    public function show($veiculoId)
    {
        $status = VeiculoStatus::where('veiculoId', $veiculoId)
            ->orderBy('timestamp', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }
}
