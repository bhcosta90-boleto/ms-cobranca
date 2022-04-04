<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PJBank\Package\Services\ValidateTrait;
use PJBank\Package\Support\ConsumeSupport;

class SincronizacaoContaCommand extends Command
{
    use ValidateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conta:sincronizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronização das contas';

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
        $consumeSupport->consume('contas', [
            'banco_emissor_id' => 'nullable',
            'valor_taxa' => 'nullable',
            'agencia_id' => 'nullable',
            'credencial' => 'required',
            'chave' => 'required',
            'tipo' => 'nullable',
            'status' => 'nullable',
        ], "app.ms_contas.table.contas.*", 'credencial');
    }
}
