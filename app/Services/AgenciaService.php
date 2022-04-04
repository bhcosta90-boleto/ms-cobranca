<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class AgenciaService
{
    public function get(string $uuid, string $campo = null)
    {
        $obj = DB::table('agencias')->where('uuid', $uuid)->first();

        if (!empty($campo)) {
            return $obj?->$campo;
        }

        return $obj;
    }
}
