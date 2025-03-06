<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigVeiculo extends Model
{
    use HasFactory;

    protected $table = 'config_veiculos';

    protected $fillable = [
        'tipo',
        'marca',
        'modelo',
        'ano_fabricacao',
        'ano_modelo',
        'placa',
        'renavam',
        'chassi',
        'cor',
        'quilometragem',
        'combustivel',
        'valor_venda',
        'valor_diaria',
        'disponivel_venda',
        'disponivel_locacao',
        'status',
        'deleted',
        'token'
    ];
}
