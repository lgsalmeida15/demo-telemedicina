<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company; // Importe o seu modelo de empresa

class CompanySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dados de 5 empresas de teste
        $companies = [
            [
                'name' => 'Tech Solutions S.A.',
                'cnpj' => '12.345.678/0001-90',
                'email' => 'contato@techsolutions.com',
                // Adicione aqui outros campos obrigatórios do seu modelo Company, se houver.
            ],
            [
                'name' => 'Alpha Marketing Digital',
                'cnpj' => '98.765.432/0001-12',
                'email' => 'vendas@alphamarketing.com',
            ],
            [
                'name' => 'Construtora Beta Ltda.',
                'cnpj' => '45.678.901/0001-34',
                'email' => 'financeiro@construtorabeta.com',
            ],
            [
                'name' => 'Health & Wellness Services',
                'cnpj' => '21.098.765/0001-56',
                'email' => 'rh@healthwellness.com',
            ],
            [
                'name' => 'Global Imports & Exports',
                'cnpj' => '33.444.555/0001-77',
                'email' => 'suporte@globalimports.com',
            ],
        ];

        // Cria as empresas usando o método create() do Eloquent
        foreach ($companies as $companyData) {
            Company::create($companyData);
        }
    }
}
