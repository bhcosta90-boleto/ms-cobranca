<?php

namespace App\Services;

use App\Models\ContaSplit;
use Illuminate\Support\Facades\DB;
use PJBank\Package\Support\SaveDataInCache;

final class ContaSplitService
{
    use SaveDataInCache;

    public function cadastrarNovaConta($data, string $tipo = null)
    {
        return $this->saveInData(function () use ($data, $tipo) {
            return ContaSplit::firstOrCreate([
                'banco' => $data['banco'],
                'documento' => $data['documento'],
                'agencia' => $data['agencia'],
                'conta' => $data['conta'],
                'tipo' => $tipo,
            ], $data);
        }, $data['banco'] . $data['documento'] . $data['agencia'] . $data['conta'] . $tipo);
    }

    public function find(int $id)
    {
        return $this->saveInData(fn () => DB::table('conta_splits')->select()->where('id', $id)->first(), $id);
    }
}
