<?php

namespace App\Models;

use App\Services\CobrancaSplitService;
use App\Services\ContaService;
use App\Services\ContaSplitService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use PJBank\Package\Models\Traits\SendQueue;
use PJBank\Package\Models\Traits\UuidGenerate;

class Cobranca extends Model
{
    use SendQueue, UuidGenerate;

    const TAXA_BOLETO = 4.00;
    const TAXA_CARTAO = 4.00;
    const TAXA_SPLIT = 1.00;

    public $fillable = [
        'conta_id',
        'banco_id',
        'vencimento',
        'cliente_nome',
        'cliente_documento',
        'valor',
        'taxa_atual',
        'numero_banco',
        'data_confirmacao',
        'data_pagamento',
        'data_credito',
        'data_repasse',
        'data_baixa',
        'pago',
        'md5',
        'hashpagamento',
        'cobrancaduplicada_id',
        'valor_pago',
    ];

    public function splits()
    {
        return $this->hasMany(CobrancaSplit::class);
    }

    public function sendQueue()
    {
        $splits = $this->splits->toArray();

        $arraySplit = [];

        foreach ($splits as $split) {
            $objContaSplit = $this->getContaSplitService()->find($split['conta_split_id']);
            try {
                $statusConta = $this->getContaService()->get($objContaSplit->credencial, 'status');

                if ($statusConta == 'aprovado') {
                    $arraySplit[] = $split + [
                        'credencial' => $objContaSplit->credencial,
                    ];
                }
            } catch (Exception $e) {
                if ($e->getCode() !== 400) {
                    throw $e;
                }
            }
        }

        $objConta = $this->getContaService()->find($this->conta_id);
        $valorTaxaCobranca = $this->getContaService()->getTaxa($objConta->credencial, $objConta->tipo);

        $valorCalculoSplit = $this->valor_pago ?: $this->valor;
        $valorTransferencia = $valorCalculoSplit - $valorTaxaCobranca;

        if (count($arraySplit)) {
            $valorTaxaSplit = 0;
            foreach ($arraySplit as $split) {
                $objContaSplit = $this->getContaSplitService()->find($split['conta_split_id']);
                $valorTaxaSplit += $this->getContaService()->getTaxa($objContaSplit->credencial, 'split');
            }

            $splits = $this->getCobrancaSplitService()->calcularValoresSplit(
                $valorCalculoSplit,
                $valorTaxaCobranca,
                $valorTaxaSplit,
                $arraySplit
            );


            foreach($splits as &$rsSplit) {
                $objContaSplit = $this->getContaSplitService()->find($split['conta_split_id']);
                $rsSplit['taxa_split'] = $this->getContaService()->getTaxa($objContaSplit->credencial, 'split');
            }

            $arraySplit = $this->getCobrancaSplitService()->calcularValorParaEnviarNoExtrato(
                $valorCalculoSplit - $valorTaxaCobranca,
                $splits
            );

            $valorTransferencia = $arraySplit['valor_transferencia'];
        }

        return [
            'credencial' => $objConta->credencial,
            'taxa_cobranca' => $valorTaxaCobranca,
            'valor_pago' => $this->valor_pago,
            'valor_transferencia' => $valorTransferencia,
            'splits' => $arraySplit['splits'] ?? [],
        ];
    }

    private function getContaSplitService(): ContaSplitService
    {
        return app(ContaSplitService::class);
    }

    private function getContaService(): ContaService
    {
        return app(ContaService::class);
    }

    private function getCobrancaSplitService(): CobrancaSplitService
    {
        return app(CobrancaSplitService::class);
    }
}
