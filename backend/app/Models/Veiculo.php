<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'modelo',
        'marca',
        'ano',
        'status',
        'quilometragem',
        'motorista_id',
        'chassi',
        'cor',
        'observacoes',
    ];

    protected $casts = [
        'quilometragem' => 'float',
        'ano' => 'integer',
    ];

    public function motorista(): BelongsTo
    {
        return $this->belongsTo(Motorista::class);
    }

    // app/Models/Veiculo.php
    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class);
    }
}
