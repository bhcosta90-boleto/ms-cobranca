<?php

namespace App\Http\Controllers;

use App\Models\BancoNumero;
use App\Services\CobrancaService;
use App\Services\ContaService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CobrancaController extends Controller
{
    public function store(Request $request, CobrancaService $cobrancaService)
    {
        $result = [];

        $data = $this->validate($request, [
            'credencial' => 'required|exists:contas,credencial',
            'cobrancas' => 'required|array|min:0'
        ]);

        $statusCode = 201;

        $objConta = $this->getContaService()->get($data['credencial']);
        $bancoEmissor = $this->getContaService()->getBancoEmissor($data['credencial']);

        if ($objConta->tipo === null) {
            BancoNumero::createTable($bancoEmissor);
        }

        DB::beginTransaction();

        foreach ($request->cobrancas ?: [] as $rs) {
            try {
                if ($objConta->tipo === null) {
                    $result[] = $cobrancaService->cadastrarBoleto($objConta, $bancoEmissor, [
                        'credencial' => $data['credencial']
                    ] + $rs);
                }

                DB::commit();
            } catch (\Illuminate\Validation\ValidationException $e) {
                $result[] = [
                    'status' => $e->status,
                    'message' => $e->errors(),
                ];

                Log::error($data);

                DB::rollBack();
                $statusCode = 200;
            } catch (Exception $e) {
                $result[] = [
                    'status' => $e->getCode(),
                    'message' => $e->getMessage(),
                ];

                Log::error($data);

                DB::rollBack();
                $statusCode = 200;
            }
        }
        return response()->json($result, $statusCode);
    }

    public function get(CobrancaService $cobrancaService, string $uuid)
    {
        return [
            'data' => $cobrancaService->get($uuid)
        ];
    }

    public function operacao(CobrancaService $cobrancaService, string $uuid, Request $request)
    {
        if (!app()->environment('local1')) {
            abort(401);
        }
        return $cobrancaService->atualizarOperacao($request->all());
    }

    private function getContaService(): ContaService
    {
        return app(ContaService::class);
    }
}
