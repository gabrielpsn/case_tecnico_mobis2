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

    public function getTelemetry()
    {
        // Busca todos os veículos
        $veiculos = \App\Models\Veiculo::all();

        // Para cada veículo, busca as localizações e status mais recentes
        $veiculosComTelemetria = $veiculos->map(function ($veiculo) {
            // Busca a última localização
            $ultimaLocalizacao = \App\Models\VeiculoLocalizacao::where('veiculoId', $veiculo->id)
                ->orderBy('timestamp', 'desc')
                ->first();

            // Busca o último status
            $ultimoStatus = \App\Models\VeiculoStatus::where('veiculoId', $veiculo->id)
                ->orderBy('timestamp', 'desc')
                ->first();

            return [
                'id' => $veiculo->id,
                'placa' => $veiculo->placa,
                'modelo' => $veiculo->modelo,
                'marca' => $veiculo->marca,
                'status' => $veiculo->status,
                'localizacao' => $ultimaLocalizacao->localizacao,
                'status_atual' => $ultimoStatus,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $veiculosComTelemetria,
        ]);
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

    /**
     * Obtém a telemetria detalhada de um veículo específico
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getVeiculoTelemetry($id)
    {
        try {
            // Busca o veículo pelo ID
            $veiculo = \App\Models\Veiculo::find($id);

            if (! $veiculo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veículo não encontrado',
                ], 404);
            }

            // Busca as localizações do veículo (últimas 100 por padrão)
            $localizacoes = \App\Models\VeiculoLocalizacao::where('veiculoId', $veiculo->id)
                ->orderBy('timestamp', 'desc')
                ->take(100)
                ->get();

            // Busca os status do veículo (últimos 50 por padrão)
            $status = \App\Models\VeiculoStatus::where('veiculoId', $veiculo->id)
                ->orderBy('timestamp', 'desc')
                ->take(50)
                ->get();

            // Busca a última localização
            $ultimaLocalizacao = $localizacoes->first();

            // Busca o último status
            $ultimoStatus = $status->first();

            // Prepara os dados de retorno
            $dados = [
                'veiculo' => [
                    'id' => $veiculo->id,
                    'placa' => $veiculo->placa,
                    'modelo' => $veiculo->modelo,
                    'marca' => $veiculo->marca,
                    'ano' => $veiculo->ano,
                    'cor' => $veiculo->cor,
                    'chassi' => $veiculo->chassi,
                    'status_atual' => $veiculo->status,
                    'quilometragem' => $veiculo->quilometragem,
                    'motorista_atual' => $veiculo->motorista ? [
                        'id' => $veiculo->motorista->id,
                        'nome' => $veiculo->motorista->nome,
                        'cpf' => $veiculo->motorista->cpf,
                        'telefone' => $veiculo->motorista->telefone,
                    ] : null,
                ],
                'ultima_localizacao' => $ultimaLocalizacao ? [
                    'id' => $ultimaLocalizacao->id,
                    'timestamp' => $ultimaLocalizacao->timestamp,
                    'localizacao' => $ultimaLocalizacao->localizacao,
                    'velocidade' => $ultimaLocalizacao->localizacao['velocidade'] ?? null,
                    'endereco' => $ultimaLocalizacao->localizacao['endereco'] ?? null,
                ] : null,
                'ultimo_status' => $ultimoStatus ? [
                    'id' => $ultimoStatus->id,
                    'timestamp' => $ultimoStatus->timestamp,
                    'status' => $ultimoStatus->status,
                    'codigo' => $ultimoStatus->codigo,
                    'descricao' => $ultimoStatus->descricao,
                    'nivel_bateria' => $ultimoStatus->nivel_bateria,
                    'nivel_combustivel' => $ultimoStatus->nivel_combustivel,
                    'quilometragem' => $ultimoStatus->quilometragem,
                    'mensagem' => $ultimoStatus->mensagem,
                ] : null,
                'historico_localizacoes' => $localizacoes->map(function ($loc) {
                    return [
                        'id' => $loc->id,
                        'timestamp' => $loc->timestamp,
                        'localizacao' => $loc->localizacao,
                        'velocidade' => $loc->localizacao['velocidade'] ?? null,
                        'endereco' => $loc->localizacao['endereco'] ?? null,
                    ];
                }),
                'historico_status' => $status->map(function ($stat) {
                    return [
                        'id' => $stat->id,
                        'timestamp' => $stat->timestamp,
                        'status' => $stat->status,
                        'codigo' => $stat->codigo,
                        'descricao' => $stat->descricao,
                        'nivel_bateria' => $stat->nivel_bateria,
                        'nivel_combustivel' => $stat->nivel_combustivel,
                        'quilometragem' => $stat->quilometragem,
                        'mensagem' => $stat->mensagem,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'data' => $dados,
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao buscar telemetria do veículo: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados de telemetria',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
