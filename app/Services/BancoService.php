<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

final class BancoService
{
    public function get(string $uuid, string $campo = null)
    {
        $obj = DB::table('bancos')->where('uuid', $uuid)->first();

        if (empty($obj)) {
            throw new Exception('Credencial não existe em nossa base de dados', Response::HTTP_BAD_REQUEST);
        }

        if (!empty($campo)) {
            return $obj->$campo;
        }

        return $obj;
    }

    public function getPrincipal()
    {
        $obj = DB::table('bancos')->orderBy('principal', 'desc')->orderBy('id', 'desc')->first();

        if (empty($obj)) {
            throw new Exception('Banco não existe em nossa base de dados', Response::HTTP_BAD_REQUEST);
        }

        return $obj;
    }
}
