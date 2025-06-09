<?php

namespace App\Http\Controllers;

use App\Models\VeiculoLocalizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VeiculoLocalizacaoController extends Controller
{
    /**
     * Armazena uma nova localização de veículo
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'veiculoId' => 'required|string',
            'timestamp' => 'required|date',
            'localizacao' => 'required|array',
            'localizacao.latitude' => 'required|numeric',
            'localizacao.longitude' => 'required|numeric',
            'localizacao.velocidade' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $localizacao = VeiculoLocalizacao::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $localizacao,
        ], 201);
    }

    /**
     * Lista as localizações de um veículo
     *
     * @param  string  $veiculoId
     * @return \Illuminate\Http\Response
     */
    public function show($veiculoId)
    {
        $localizacoes = VeiculoLocalizacao::where('veiculoId', $veiculoId)
            ->orderBy('timestamp', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $localizacoes,
        ]);
    }
}
