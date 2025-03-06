<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigFuncionario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf',
        'salario',
        'cargo',
        'status',
        'token',
        'deleted',
    ];
}
