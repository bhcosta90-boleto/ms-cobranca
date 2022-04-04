<?php

namespace App\Support;

use Exception;
use Illuminate\Support\Facades\DB;

final class SynchronizeTable
{
    public static function sincronizar(string $table, $key, $value, $data)
    {
        try {
            return DB::table($table)->updateOrInsert([
                $key => $value
            ], $data);
        } catch(Exception $e) {
            dump($data);
            throw $e;
        }
    }
}
