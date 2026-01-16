<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CaixaSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hoje = Carbon::today();

        // $caixas = [
        //     ['descricao' => 'Fundo Fixo'],
        //     ['descricao' => 'Caixa Sede Social'],
        //     ['descricao' => 'Caixa Sede Campestre'],
        //     ['descricao' => 'Banco COIMPPA'],
        //     ['descricao' => 'Banco CEF'],
        // ];
        $caixas = [
            ['descricao' => 'Caixa Teste'],
            ['descricao' => 'Caixa Exemplo'],
        ];

        foreach ($caixas as $caixa) {
            DB::table('caixas')->insert([
                'descricao'   => $caixa['descricao'],
                'obs'         => null,
                'cadastro'    => $hoje,
                'atualizacao' => null,
                'exclusao'    => null,

            ]);
        }
    }
}
