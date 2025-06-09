<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class VeiculoStatus extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'veiculo_status';

    protected $fillable = [
        'veiculoId',
        'timestamp',
        'status',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'status' => 'array',
    ];
}
