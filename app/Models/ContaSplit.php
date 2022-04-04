<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PJBank\Package\Models\Traits\SendQueue;

class ContaSplit extends Model
{
    use SendQueue;

    private $_chave;

    public static function booted(): void
    {
        parent::creating(function ($obj) {
            $obj->credencial = sha1(str()->uuid());
            $obj->chave = sha1($obj->_chave = str()->uuid());
        });
    }

    protected $fillable = [
        'nome',
        'banco',
        'agencia',
        'conta',
        'documento',
    ];

    public function sendQueue()
    {
        $obj = $this->toArray();

        return $obj + [
            'split' => true,
            'chave' => sha1($this->_chave),
            'cpfcnpj' => $this->documento
        ];
    }
}
