<?php

namespace App\Services;

use App\Models\CobrancaSplit;
use Exception;
use Illuminate\Support\Facades\Log;

final class CobrancaSplitService
{
    public function __construct(private CobrancaSplit $repository)
    {
        //
    }

    public function calcularValoresSplit(
        float $valorCobranca,
        float $tarifaCobranca,
        float $valorTarifaSplit,
        array $data
    ) {
        $testValorFixo = $testPorcentagem = 0;
        $ret = [];

        foreach ($data as $item) {
            $valorPorcentagem = $item['porcentagem'] ?? null;

            if (isset($item['valor'])) {
                if (!preg_match('#^[0-9\.]{1,9}$#', $item['valor'])) {
                    throw new Exception('Valor do split inválido.', 400);
                } else if ($item['valor'] == 0) {
                    throw new Exception('Valor do split não pode ser 0.', 400);
                }
                $testValorFixo += $item['valor'];
            } else if ($valorPorcentagem) {
                if ((!preg_match('#^[0-9\.]{1,5}$#', $valorPorcentagem)) || ($valorPorcentagem > 100)) {
                    throw new Exception('Porcentagem inválida', 400);
                } else if ($valorPorcentagem == 0) {
                    throw new Exception('Porcentagem do split não pode ser 0.', 400);
                }

                $testPorcentagem += $valorPorcentagem;
            }

            $ret[] = $item;
        }

        if ((round($testValorFixo, 2) > 0) && (round($testPorcentagem, 2) > 0)) {
            throw new Exception('Valor fixo e percentual no mesmo split.', 400);
        }

        if (round($testValorFixo, 2) == 0 && round($testPorcentagem, 2) == 0) {
            throw new Exception('Nenhum valor informado para split.', 400);
        }

        if (round($testPorcentagem, 2) < 0) {
            throw new Exception('Percentual do split não pode ser menor que 0.', 400);
        }

        $valorPermitido = round($valorCobranca - $tarifaCobranca - $valorTarifaSplit, 2);

        if ($valorPermitido < round($testValorFixo, 2)) {
            throw new Exception(
                __("Valor do split maior do que o permitido para a cobrança, valor permitido :valor.", [
                    'valor' => $valorPermitido,
                ]),
                400
            );
        }

        if ($testPorcentagem > 100) {
            throw new Exception(
                __("Soma das porcentagens do split excede o limite do valor permitido de :valor.", [
                    'valor' => 100,
                ]),
                400
            );
        }

        return $ret;
    }

    public function calcularValorParaEnviarNoExtrato(float $valor, array $splits)
    {
        $ret = [
            'valor_transferencia' => $valor,
            'splits' => [],
        ];
        foreach ($splits as $rs) {
            $valorCalculado = $rs['valor'] ?? null;
            if (empty($valorCalculado)) {
                $valorCalculado = $valor * ($rs['porcentagem'] / 100);
            }

            $ret['splits'][] = [
                'valor_split' => $valorCalculado,
                'valor_transferencia' => $valorCalculado - $rs['taxa_split'],
                'credencial' => $rs['credencial'],
                'tarifa' => $rs['taxa_split'],
            ];

            $ret['valor_transferencia'] -= $valorCalculado;
        }

        Log::info($ret);
        return $ret;
    }

    public function cadastrarNovoSplitNaCobranca(int $idCobranca, $dataSplit)
    {
        return $this->repository->create([
            'cobranca_id' => $idCobranca
        ] + $dataSplit);
    }
}
