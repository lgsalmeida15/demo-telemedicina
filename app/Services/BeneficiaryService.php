<?php


namespace App\Services;

use App\Models\Beneficiary;
use Illuminate\Http\Request;
use App\Services\AsaasCustomerService;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class BeneficiaryService
{
    /**
     * Cria um novo beneficiario;
     * usa service do assas customer para criar o customer id do asaas
     */
    public function createBeneficiary($request, $companyUuid)
    {
        $data = $request->validate(
            [
                'name' => 'required',
                'cpf' => 'required',
                'email' => 'required',
                'phone' => 'required',
                'birth_date' => 'required',
                'gender' => 'required',
                'password' => 'required',
                'mother_name' => 'required',
            ],
            [
                'name.required' => 'O campo nome é obrigatório.',
                'cpf.required' => 'O campo CPF é obrigatório.',
                'email.required' => 'O campo email é obrigatório.',
                'phone.required' => 'O campo telefone é obrigatório.',
                'birth_date.required' => 'O campo data de nascimento é obrigatório.',
                'gender.required' => 'O campo gênero é obrigatório.',
                'password.required' => 'O campo senha é obrigatório.',
                'mother_name.required' => 'O campo nome da mãe é obrigatório.',
            ]
        );

        try {

            $companyId = Company::where('uuid', $companyUuid)->firstOrFail()->id;

            // 🔍 1. VERIFICA SE BENEFICIÁRIO JÁ EXISTE
            $existing = Beneficiary::where('cpf', $data['cpf'])
                ->orWhere('email', $data['email'])
                ->first();

            if ($existing) {

                // 🔄 Se existir mas ainda não tiver customer no Asaas — cria agora
                if (!$existing->asaas_customer_id) {
                    $customerId = app(AsaasCustomerService::class)
                        ->createCustomerForBeneficiary($existing);

                    $existing->update([
                        'asaas_customer_id' => $customerId
                    ]);
                }

                return $existing; // retorna o beneficiário existente
            }


            // 🆕 2. SE NÃO EXISTIR → CRIA
            $beneficiary = Beneficiary::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'cpf' => $data['cpf'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'relationship' => 'Titular',
                'mother_name' => $data['mother_name'],
                'password' => Hash::make($data['password'])
            ]);

            // 🔗 3. CRIA customer no Asaas
            $customerId = app(AsaasCustomerService::class)
                ->createCustomerForBeneficiary($beneficiary);

            $beneficiary->update([
                'asaas_customer_id' => $customerId
            ]);

            return $beneficiary;

        } catch (\Exception $e) {
            throw $e;
        }
    }

}