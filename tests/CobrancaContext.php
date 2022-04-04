<?php

namespace Tests;

use Behat\Gherkin\Node\TableNode;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use PJBank\Package\Behat\LaravelContext;

final class CobrancaContext extends LaravelContext
{
    /**
     * @Given Eu estou criando uma nova credencial
     */
    public function euEstouCriandoUmaNovaCredencial(TableNode $changes = null)
    {
        $changesArray = $this->mapTableNodeToArray($changes);

        DB::table('contas')->insert([
            'credencial' => $credencial = sha1(str()->uuid()),
            'chave' => $chave = sha1($chave = str()->uuid()),
        ] + $changesArray);

        self::$storage['credencial'][] = $credencial;
        self::$storage['chave'][] = $chave;
    }

    /**
     * @Given Eu estou criando uma nova cobrança
     * @Given Eu estou criando uma nova cobrança com status :status
     */
    public function euEstouCriandoUmaCobranca($status = 201, TableNode $changes = null)
    {
        $changesArray = $this->mapTableNodeToArray($changes);

        $data = [
            "credencial" => end(self::$storage['credencial']),
            "cobrancas" => [
                $changesArray + [
                    "cliente_nome" => $this->_time(),
                    "vencimento" => Carbon::now()->format('d/m/Y'),
                    "valor" => rand(50, 100)
                ]
            ]
        ];

        $dataResponse = $this->sendingUrl('/cobrancas', 'POST', $status == 201 ? $status : 200, $data);
        if ($status == 201) {
            $uuid = $dataResponse[0]['uuid'];
            $this->euEstouValidandoCobranca($uuid, $changesArray);
        }
    }

    private function euEstouValidandoCobranca(string $uuid, $dataValidated)
    {
        $dataValidarCobrancas = $dataValidated;
        $dataValidarSplits = $dataValidated['splits'] ?? [];

        unset($dataValidarCobrancas['splits']);

        if (count($dataValidarCobrancas) == 0 && count($dataValidarSplits) == 0) {
            return false;
        }

        if (count($dataValidarCobrancas)) {
            $this->validatingTableWithField("cobrancas", "uuid", $uuid, $dataValidarCobrancas);
        }

        if (count($dataValidarSplits)) {
            $dataSplits = DB::table('cobranca_splits')
                ->select([
                    'cobranca_splits.valor',
                    'cobranca_splits.porcentagem',
                    'conta_splits.nome',
                    'conta_splits.documento',
                    'conta_splits.banco',
                    'conta_splits.agencia',
                    'conta_splits.conta',
                ])
                ->join('cobrancas', 'cobrancas.id', '=', 'cobranca_splits.cobranca_id')
                ->join('conta_splits', 'conta_splits.id', '=', 'cobranca_splits.conta_split_id')
                ->where('cobrancas.uuid', $uuid)
                ->orderBy('cobranca_splits.id')
                ->get()
                ->toArray();

            foreach ($dataValidarSplits as $key => $splits) {
                foreach ($splits as $field => $split) {
                    try {
                        $this->assertEquals($dataSplits[$key]->$field, $split);
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            }
        }
    }
}
