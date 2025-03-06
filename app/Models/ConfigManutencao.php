<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigManutencao extends Model
{
    use HasFactory;

    protected $table = 'config_manutencoes';

    protected $fillable = [
        'id_veiculo',
        'data_manutencao',
        'descricao',
        'valor',
        'tipo',
        'token',
        'status',
        'deleted'
    ];

    public function ConfigVeiculos()
    {
        return $this->belongsTo(ConfigVeiculo::class, 'id_veiculo', 'id');
    }
    

    
}
