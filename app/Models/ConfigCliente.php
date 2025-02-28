<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf',
        'data_nascimento',
        'telefone',
        'email',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'cidade',
        'estado',
        'token',
        'deleted'
    ];
}
