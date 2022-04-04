<?php

namespace App\Services;

use App\Models\Cobranca;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PJBank\Package\Support\SaveDataInCache;

final class ContaService
{
    use SaveDataInCache;

    public function get(string $credencial, string $campo = null)
    {
        $obj = $this->getByCredencial($credencial);

        if (!empty($campo)) {
            return $obj?->$campo;
        }

        return $obj;
    }

    public function find(int $id) {
        return $this->saveInData(fn () => DB::table('contas')->select()->where('id', $id)->first(), $id);
    }

    public function getTaxa(string $credencial, string $tipo = null)
    {
        return $this->saveInData(function () use ($credencial, $tipo) {
            $obj = $this->getByCredencial($credencial, false);

            $valorTaxa = $obj?->valor_taxa;

            if ($obj && empty($valorTaxa) && $obj->agencia_id) {
                $valorTaxa = $this->getAgenciaService()->get($obj->agencia_id, 'valor_taxa');
            }

            if (empty($valorTaxa)) {
                $valorTaxa = match ($tipo) {
                    'split' => Cobranca::TAXA_SPLIT,
                    'cartao' => Cobranca::TAXA_CARTAO,
                    default => Cobranca::TAXA_BOLETO
                };
            }

            return $valorTaxa ?: 0;
        }, "{$credencial}{$tipo}");
    }

    public function getBancoEmissor(string $credencial)
    {
        return $this->saveInData(function () use ($credencial) {
            $bancoEmissor = $this->getByCredencial($credencial)?->banco_emissor_id;

            if (empty($bancoEmissor)) {
                $bancoEmissor = $this->getAgenciaService()->get($credencial, 'banco_emissor_id');
            }

            if (empty($bancoEmissor)) {
                $bancoEmissor = $this->getBancoService()->getPrincipal()->uuid;
            }

            return $bancoEmissor;
        }, $credencial);
    }

    private function getByCredencial(string $credencial, $valid = true)
    {
        return $this->saveInData(function () use ($credencial, $valid) {
            $obj = DB::table('contas')->where('credencial', $credencial)->first();

            if ($valid == true && empty($obj)) {
                throw new Exception('Credencial não existe em nossa base de dados', Response::HTTP_BAD_REQUEST);
            }

            return $obj;
        }, $credencial . $valid);
    }

    private function getAgenciaService(): AgenciaService
    {
        return app(AgenciaService::class);
    }

    private function getBancoService(): BancoService
    {
        return app(BancoService::class);
    }
}
