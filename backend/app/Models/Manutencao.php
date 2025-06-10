<?php

namespace App\Models;

use App\Enums\TipoManutencao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manutencao extends Model
{
    use HasFactory;

    protected $table = 'manutencoes'; // Nome correto da tabela

    protected $fillable = [
        'veiculo_id',
        'data_manutencao',
        'descricao',
        'tipo',
        'km',
        'custo',
    ];

    protected $casts = [
        'data_manutencao' => 'date:d/m/Y',
        'km' => 'float',
        'custo' => 'float',
        'tipo' => TipoManutencao::class,
    ];

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }
}
