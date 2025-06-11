<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MongoDB\Laravel\Eloquent\HybridRelations;

class Veiculo extends Model
{
    use HasFactory, HybridRelations;

    protected $connection = 'mysql'; // Mantém a conexão com MySQL para o modelo principal

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

    public function manutencoes(): HasMany
    {
        return $this->hasMany(Manutencao::class);
    }

    // Relacionamento com localizações (MongoDB)
    public function localizacoes()
    {
        return $this->hasMany(VeiculoLocalizacao::class, 'veiculoId');
    }

    // Relacionamento com status (MongoDB)
    public function status()
    {
        return $this->hasMany(VeiculoStatus::class, 'veiculoId');
    }

    /**
     * Obtém a última localização do veículo
     *
     * @return \App\Models\VeiculoLocalizacao|null
     */
    public function ultimaLocalizacao()
    {
        return $this->hasOne(VeiculoLocalizacao::class, 'veiculoId')
            ->orderBy('timestamp', 'desc')
            ->limit(1);
    }

    /**
     * Obtém o último status do veículo
     *
     * @return \App\Models\VeiculoStatus|null
     */
    public function ultimoStatus()
    {
        return $this->hasOne(VeiculoStatus::class, 'veiculoId')
            ->orderBy('timestamp', 'desc')
            ->limit(1);
    }
}
