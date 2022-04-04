<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BancoNumero extends Model
{
    public $timestamps = false;

    public static function createTable(string $uuid)
    {
        if (Schema::hasTable($table = ('_bn' . sha1($uuid))) == false) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
            });
        }
    }

    public static function generate(string $uuid)
    {
        $obj = new BancoNumero();
        $obj->setTable('_bn' . sha1($uuid));
        $ret = $obj->create();

        return $ret->id;
    }
}
