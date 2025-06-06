<?php

namespace App\Http\Requests\Motorista;

use Illuminate\Foundation\Http\FormRequest;

class MotoristaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|size:14|unique:motoristas,cpf',
            'data_nascimento' => 'required|date',
            'categoria_cnh' => 'required|string|size:1|in:A,B,C,D,E,AB,AC,AD,AE',
            'telefone' => 'required|string|max:20',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $motorista = $this->route('motorista');
            $rules['cpf'] = 'required|string|size:14|unique:motoristas,cpf,'.$motorista;
        }

        return $rules;
    }
}
