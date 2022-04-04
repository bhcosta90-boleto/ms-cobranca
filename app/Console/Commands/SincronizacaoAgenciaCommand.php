<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PJBank\Package\Services\ValidateTrait;
use PJBank\Package\Support\ConsumeSupport;

class SincronizacaoAgenciaCommand extends Command
{
    use ValidateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agencia:sincronizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronização das agências';

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
        $consumeSupport->consume('agencias', [
            'uuid' => 'required',
            'banco_emissor_id' => 'nullable',
            'valor_taxa' => 'nullable',
        ], "app.ms_contas.table.agencias.*", 'uuid');
    }
}
