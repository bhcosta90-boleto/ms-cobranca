<?php

namespace App\Services;

use App\Models\BancoNumero;
use App\Models\Cobranca;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use PJBank\Package\Services\ValidateTrait;

final class CobrancaService
{
    use ValidateTrait;

    public function __construct(protected Cobranca $repository)
    {
        //
    }

    public function get(string $uuid)
    {
        return $this->repository->where('uuid', $uuid)->firstOrFail()->sendQueue();
    }

    public function cadastrarBoleto($objConta, string $bancoEmissor, array $data)
    {
        $rules = [
            'cliente_nome' => 'required|min:3|max:150',
            'cliente_documento' => 'nullable|min:3|max:30',
            'vencimento' => 'required|date_format:d/m/Y',
            'valor' => 'required|numeric|min:' . $objConta->valor_minimo
        ];

        $dataCobranca = $this->validate($data, [
            'credencial' => 'required'
        ] + $rules);

        $dataCobranca['numero_banco'] = BancoNumero::generate($bancoEmissor);
        $dataCobranca['vencimento'] = Carbon::createFromFormat('d/m/Y', $dataCobranca['vencimento'])->format('Y-m-d');

        $valorTaxaCobranca = $this->getContaService()->getTaxa($objConta->credencial, $objConta->tipo);

        $ret = $this->repository->create($dataCobranca + [
            'conta_id' => $objConta->id,
            'banco_id' => $bancoEmissor,
            'taxa_atual' => $valorTaxaCobranca
        ]);


        if (isset($data['splits'])) {

            $rulesSplit = [
                'splits' => 'required|array|min:1',
                'splits.*.nome' => 'required|string|min:3|max:150',
                'splits.*.documento' => 'required|string|min:11|max:14',
                'splits.*.banco' => 'required|string|min:3|max:3',
                'splits.*.agencia' => 'required|string|min:2|max:20',
                'splits.*.conta' => 'required|string|min:2|max:20',
                'splits.*.valor' => 'nullable|numeric|min:0',
                'splits.*.porcentagem' => 'nullable|numeric|min:0|max:100',
            ];

            $dataSplit = $this->validate($data, $rulesSplit);

            $valorTaxaSplit = 0;
            foreach ($dataSplit['splits'] as $split) {
                $objContaSplit = $this->getContaSplitService()->cadastrarNovaConta($split, $objConta->tipo);
                $valorTaxaSplit += $this->getContaService()->getTaxa($objContaSplit->credencial, 'split');
            }

            $splits = $this->getCobrancaSplitService()->calcularValoresSplit(
                $dataCobranca['valor'],
                $valorTaxaCobranca,
                $valorTaxaSplit,
                $dataSplit['splits']
            );

            foreach ($splits as $split) {
                if (empty($split['valor']) && empty($split['porcentagem'])) {
                    throw ValidationException::withMessages(['valor' => 'É obrigatório definir ou valor ou porcentagem']);
                }

                $objContaSplit = $this->getContaSplitService()->cadastrarNovaConta($split, $objConta->tipo);

                $this->getCobrancaSplitService()->cadastrarNovoSplitNaCobranca($ret->id, $split + [
                    'conta_split_id' => $objContaSplit->id,
                    'taxa_atual' => $this->getContaService()->getTaxa($objContaSplit->credencial, 'split')
                ]);
            }
        }

        return $ret;
    }

    public function atualizarOperacao($data)
    {
        $dataValidated = $this->validate($data, [
            'recebimento_id' => 'required',
            'banco_id' => 'required',
            'operacao' => 'required',
            'nomearquivo' => 'required',
            'hashfile' => 'required',
            'valor_cobranca' => 'required',
            'valor_pago' => 'required',
        ]);

        $dataUpdated = [];

        $objCobranca = $this->repository->where('banco_id', $dataValidated['banco_id'])
            ->where('numero_banco', $dataValidated['recebimento_id'])
            ->first();

        $dispararExtrato = empty($objCobranca->pago) ? true : false;

        if ($dataValidated['operacao'] == '02') {
            $dataUpdated['data_confirmacao'] = Carbon::now();
        }

        if ($dataValidated['operacao'] == '06') {
            $dataUpdated['md5'] = md5($dataValidated['nomearquivo']);
            $dataUpdated['hashpagamento'] = $dataValidated['hashfile'];
            $dataUpdated['data_pagamento'] = Carbon::now();
            $dataUpdated['data_credito'] = Carbon::now();
            $dataUpdated['data_repasse'] = Carbon::now();
            $dataUpdated['pago'] = true;
            $dataUpdated['valor'] = $dataValidated['valor_cobranca'];
            $dataUpdated['valor_pago'] = $dataValidated['valor_pago'];

            if ($objCobranca && $objCobranca->pago && $objCobranca->hashpagamento != $dataValidated['hashfile']) {
                $objCobrancaNova = $this->repository->create([
                    'cobrancaduplicada_id' => $objCobranca->id,
                ] + $objCobranca->toArray());

                $objCobrancaNova->sendMessage("cobrancapaga");
            }
        }


        if ($dataValidated['operacao'] == '09') {
            $dataUpdated['data_baixa'] = Carbon::now();
        }

        if ($objCobranca) {
            $objCobranca->update($dataUpdated);

            if ($dataValidated['operacao'] == '06' && $dispararExtrato) {
                $objCobranca->sendMessage("cobrancapaga");
            }

            return $objCobranca;
        }
    }

    protected function getAgenciaService(): AgenciaService
    {
        return app(AgenciaService::class);
    }

    protected function getBancoService(): BancoService
    {
        return app(BancoService::class);
    }

    protected function getContaService(): ContaService
    {
        return app(ContaService::class);
    }

    protected function getContaSplitService(): ContaSplitService
    {
        return app(ContaSplitService::class);
    }

    protected function getCobrancaSplitService(): CobrancaSplitService
    {
        return app(CobrancaSplitService::class);
    }
}
