<?php

namespace App\Services;

use App\Models\Plan;
use Exception;

class AsaasPaymentService
{
    /**
     * Summary of createPayment
     * @param mixed $b
     * @param mixed $planUuid
     * @param mixed $type
     * @throws Exception
     */
    public function createPayment($b, $planUuid, $type)
    {
        try {
            $plan = Plan::where('uuid', $planUuid)->firstOrFail();

            $url = "https://api.asaas.com/v3/payments";

            // monta o payload do pagamento
            $payload = json_encode([
                "customer" => $b->asaas_customer_id,
                "billingType" => $type,
                "value" => $plan->value,
                "dueDate" => now()->addDays(3)->format('Y-m-d'),
            ]);

            // inicia curl
            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "User-Agent: TeleMedicina",
                    "access_token:" . env("ASAAS_TOKEN")
                ],
                CURLOPT_SSL_VERIFYPEER => false, // ❌ desativa SSL (somente local)
                CURLOPT_SSL_VERIFYHOST => false, // ❌ desativa verificação hostname
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Erro de conexão CURL
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("Erro CURL ao criar pagamento no Asaas: $error");
            }

            curl_close($ch);

            $responseData = json_decode($response, true);

            // Asaas retornou erro HTTP
            if ($httpCode >= 400) {
                throw new Exception("Erro Asaas ($httpCode): " . $response);
            }

            // Asaas não retornou um payment id
            if (!isset($responseData['id'])) {
                throw new Exception("Erro ao criar pagamento no Asaas: resposta inválida.");
            }

            return $responseData; // retorna tudo igual ao Http::post()
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Summary of createSubscription
     * @param mixed $customer
     * @param mixed $value
     * @param mixed $description
     * @param mixed $creditCard
     * @param mixed $holderInfo
     * @throws Exception
     */
    public function createSubscription($customer, $value, $description, $creditCard, $holderInfo)
    {
        try {
            $url = "https://api.asaas.com/v3/subscriptions";
            // Monta o payload conforme o Asaas exige
            $payload = json_encode([
                "customer" => $customer,
                "billingType" => "CREDIT_CARD",
                "nextDueDate" => now()->format('Y-m-d'),
                "value" => $value,
                "cycle" => "MONTHLY",
                "description" => $description,
                "creditCard" => [
                    "holderName" => $creditCard['holderName'],
                    "number" => $creditCard['number'],
                    "expiryMonth" => $creditCard['expiryMonth'],
                    "expiryYear" => $creditCard['expiryYear'],
                    "ccv" => $creditCard['ccv'],
                ],
                "creditCardHolderInfo" => [
                    "name" => $holderInfo['name'],
                    "email" => $holderInfo['email'],
                    "cpfCnpj" => $holderInfo['cpfCnpj'],
                    "postalCode" => $holderInfo['postalCode'],
                    "addressNumber" => $holderInfo['addressNumber'],
                    "addressComplement" => $holderInfo['addressComplement'],
                    "phone" => $holderInfo['phone'],
                    "mobilePhone" => $holderInfo['mobilePhone'],
                ]
            ]);
            // CURL
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "User-Agent: TeleMedicina",
                    "access_token:" . env("ASAAS_TOKEN")
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception("Erro ao conectar ao Asaas: $error");
            }
            curl_close($ch);
            $data = json_decode($response, true);
            // 🛑 TRATAMENTO DE ERROS DO ASAAS
            if ($httpCode >= 400) {
                $msg = "Erro ao processar pagamento.";
                // Captura mensagem amigável
                if (isset($data['errors'][0])) {
                    $msg = $data['errors'][0]['description']
                        ?? $data['errors'][0]['code']
                        ?? $msg;
                }
                throw new \Exception($msg);
            }
            if (!isset($data['id'])) {
                throw new \Exception("Erro ao criar assinatura no Asaas: resposta inválida.");
            }
            return $data;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
