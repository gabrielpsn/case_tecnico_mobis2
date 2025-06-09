<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class VeiculoLocalizacao extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'veiculo_localizacoes';

    protected $fillable = [
        'veiculoId',
        'timestamp',
        'localizacao',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'localizacao' => 'array',
    ];
}
