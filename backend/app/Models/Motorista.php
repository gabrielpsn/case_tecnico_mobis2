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
    ];

    protected $dates = ['data_nascimento'];

    protected $casts = [
        'data_nascimento' => 'date:Y-m-d',
    ];

    public function veiculo(): HasOne
    {
        return $this->hasOne(Veiculo::class);
    }
}
