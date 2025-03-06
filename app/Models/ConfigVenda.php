<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigVenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_veiculo',
        'id_cliente',
        'valor',
        'tipo',
        'token',
        'status',
        'deleted',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'status' => 'boolean',
        'deleted' => 'boolean',
    ];

    public function veiculo()
    {
        return $this->belongsTo(ConfigVeiculo::class, 'id_veiculo', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(ConfigCliente::class, 'id_cliente', 'id');
    }
}
