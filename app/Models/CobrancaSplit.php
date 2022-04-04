<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobrancaSplit extends Model
{
    protected $fillable = [
        'cobranca_id',
        'conta_split_id',
        'valor',
        'porcentagem',
        'taxa_atual',
    ];
}
