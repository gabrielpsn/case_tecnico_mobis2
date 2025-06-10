<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Motorista extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'categoria_cnh',
        'telefone',
        'email',
        'cnh',
        'validade_cnh',
    ];

    protected $casts = [
        'data_nascimento' => 'date:d/m/Y',
        'validade_cnh' => 'date:d/m/Y',
    ];

    public function veiculo(): HasOne
    {
        return $this->hasOne(Veiculo::class);
    }
}
