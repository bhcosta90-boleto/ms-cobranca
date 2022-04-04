<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataBancos = [
            [
                'uuid' => 'ca578063-42e9-426b-8b80-2cffd34a8387',
                'codigo' => '301',
                'cnpj' => '99999999999999',
                'nome' => 'PJBank',
                'agencia' => '1234',
                'conta' => '4567',
                'carteira' => '010',
                'principal' => '1',
                'repasse' => true,
                'data' => json_encode([
                    'conta_bancaria' => '156165156156',
                ])
            ],
            [
                'uuid' => 'ca578063-42e9-426b-8b80-2cffd34a8400',
                'codigo' => '033',
                'cnpj' => '99999999999999',
                'nome' => 'PJBank',
                'agencia' => '1234',
                'conta' => '4567',
                'carteira' => '010',
                'principal' => '0',
                'repasse' => true,
                'data' => null
            ]
        ];

        DB::table('bancos')->insert($dataBancos);

        $dataConta = [
            [
                'credencial' => '2e1206a83983f29d741493d2339f59d0d67f6bb0',
                'chave' => '5f9d858b17cbf27568bfc7b8e20c5e6097370a81',
                'tipo' => null,
                'valor_taxa' => null,
            ],
            [
                'credencial' => '2e1206a83983f29d741493d2339f59d0d67f6bb1',
                'chave' => '5f9d858b17cbf27568bfc7b8e20c5e6097370a81',
                'tipo' => null,
                'valor_taxa' => 3.50,
            ],
            [
                'credencial' => 'b98e3977baf7646f45304a86bab6731ec8b34a97',
                'chave' => 'b989e3eb9d7663cd385f027c539340bdb79c6cac',
                'tipo' => 'cartao',
                'valor_taxa' => null
            ],
            [
                'credencial' => 'b98e3977baf7646f45304a86bab6731ec8b34a98',
                'chave' => 'b989e3eb9d7663cd385f027c539340bdb79c6cac',
                'tipo' => 'cartao',
                'valor_taxa' => 3.80
            ]
        ];

        DB::table('contas')->insert($dataConta);
    }
}
