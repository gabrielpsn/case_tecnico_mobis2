<?php

namespace App\Http\Requests\Veiculo;

use Illuminate\Foundation\Http\FormRequest;

class VeiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'placa' => 'required|string|size:7|unique:veiculos,placa',
            'modelo' => 'required|string|max:100',
            'marca' => 'required|string|max:50',
            'ano' => 'required|integer|min:1900|max:'.(date('Y') + 1),
            'status' => 'required|in:disponivel,em_uso,manutencao',
            'quilometragem' => 'required|numeric|min:0',
            'motorista_id' => 'nullable|exists:motoristas,id',
            'chassi' => 'required|string|size:17|unique:veiculos,chassi',
            'cor' => 'required|string|max:50',
            'observacoes' => 'nullable|string|max:255',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $veiculoId = $this->route('veiculo');
            $rules = [
                'placa' => 'sometimes|required|string|size:7|unique:veiculos,placa,'.$veiculoId,
                'modelo' => 'sometimes|required|string|max:100',
                'marca' => 'sometimes|required|string|max:50',
                'ano' => 'sometimes|required|integer|min:1900|max:'.(date('Y') + 1),
                'status' => 'sometimes|required|in:disponivel,em_uso,manutencao',
                'quilometragem' => 'sometimes|required|numeric|min:0',
                'motorista_id' => 'sometimes|nullable|exists:motoristas,id',
                'chassi' => 'sometimes|required|string|size:17|unique:veiculos,chassi,'.$veiculoId,
                'cor' => 'sometimes|required|string|max:50',
                'observacoes' => 'sometimes|nullable|string|max:255',
            ];
        }

        return $rules;
    }
}
