<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PJBank\Package\Services\ValidateTrait;
use PJBank\Package\Support\ConsumeSupport;

class SincronizacaoBancoCommand extends Command
{
    use ValidateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banco:sincronizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronização dos bancos';

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
        $consumeSupport->consume('bancos', [
            'uuid' => 'required',
            'codigo' => 'required',
            'cnpj' => 'required',
            'nome' => 'required',
            'agencia' => 'required',
            'conta' => 'required',
            'carteira' => 'required',
            'data' => 'nullable',
        ], "app.ms_bancos.table.bancos.*", 'uuid');
    }
}
