<?php

namespace App\Http\Requests\Manutencao;

use Illuminate\Foundation\Http\FormRequest;

class ManutencaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'veiculo_id' => 'required|exists:veiculos,id',
            'data_manutencao' => 'required|date',
            'descricao' => 'required|string|max:500',
            'tipo' => 'required|in:preventiva,corretiva',
            'km' => 'required|numeric|min:0',
            'custo' => 'required|numeric|min:0',
        ];
    }
}
