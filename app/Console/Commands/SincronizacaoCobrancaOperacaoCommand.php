<?php

namespace App\Console\Commands;

use App\Models\Cobranca;
use App\Services\CobrancaService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PJBank\Package\Support\ConsumeSupport;

class SincronizacaoCobrancaOperacaoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cobrancaoperacao:sincronizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConsumeSupport $consumeSupport)
    {
        $consumeSupport->service(
            "app.ms_retornos.table.retorno_items.created.operacao.*",
            CobrancaService::class,
            "atualizarOperacao"
        );
    }
}
