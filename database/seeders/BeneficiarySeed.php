<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Beneficiary; // Certifique-se de importar seu modelo
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BeneficiarySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa a tabela antes de popular (opcional, mas recomendado para seeds de teste)
        // Beneficiary::truncate(); // Descomente se quiser limpar a tabela a cada execução

        // Cria uma instância do Faker com localização brasileira
        $faker = Faker::create('pt_BR');

        // IDs fixos conforme solicitado
        $companyId = 1;
        $planId = 1;

        // Opções para campos ENUM e Strings
        $actions = ['I', 'E', 'M'];
        $genders = ['M', 'F'];
        $relationships = ['Colaborador', 'Cônjuge', 'Filho(a)', 'Pai/Mãe', 'Irmão(ã)'];

        // Cria registros
        for ($i = 0; $i < 60; $i++) {
            $gender = $faker->randomElement($genders);
            
            // Gerar nomes consistentes com o sexo para Mother's Name
            $motherName = $faker->lastName . ' ' . $faker->firstNameFemale;

            $inclusionDate = $faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d');
            $action = $faker->randomElement($actions);
            
            // Define a data de exclusão apenas para registros com ação 'E' (Exclusão)
            $exclusionDate = ($action === 'E') 
                ? $faker->dateTimeBetween($inclusionDate, 'now')->format('Y-m-d')
                : null;
            
            Beneficiary::create([
                'company_id' => $companyId,
                'plan_id' => $planId,
                'name' => $faker->name($gender == 'M' ? 'male' : 'female'),
                'cpf' => $faker->cpf(false), // Gera um CPF válido (sem formatação)
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->cellphoneNumber(false),
                'action' => $action,
                'birth_date' => $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
                'gender' => $gender,
                'relationship' => $faker->randomElement($relationships),
                'mother_name' => $motherName,
                'inclusion_date' => $inclusionDate,
                'exclusion_date' => $exclusionDate,
                // created_at e updated_at serão preenchidos automaticamente
            ]);
        }
    }
}
