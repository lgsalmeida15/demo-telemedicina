<?php

namespace App\Services;

use Exception;

class AsaasCustomerService
{
    /**
     * Cria ou recupera um customer do Asaas pelo CPF ou Email
     */
    public function createCustomerForBeneficiary($b)
    {
        try {

            // ====================================================
            // 1. CONSULTA PRIMEIRO SE JÁ EXISTE CUSTOMER NO ASAAS
            // ====================================================
            $existing = $this->findCustomer($b->cpf, $b->email);

            if ($existing) {
                return $existing['id'];
            }

            // ====================================================
            // 2. NÃO EXISTE → CRIA UM CUSTOMER NOVO
            // ====================================================
            return $this->createCustomer($b);

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Busca customer no Asaas por CPF ou email
     */
    private function findCustomer($cpf, $email)
    {
        try {
            $url = "https://api.asaas.com/v3/customers?cpfCnpj={$cpf}&email={$email}";

            $ch = curl_init($url);

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
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
                throw new Exception("Erro CURL ao consultar Asaas: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            if ($httpCode >= 400) {
                throw new Exception("Erro Asaas ao consultar cliente ($httpCode): " . $response);
            }

            // A resposta do Asaas vem assim:
            // { "data": [ { ...customer... } ], "totalCount": 1 }
            if (isset($data['data']) && count($data['data']) > 0) {
                return $data['data'][0];
            }

            return null;

        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Criação de novo customer no Asaas
     */
    private function createCustomer($b)
    {
        try {
            $url = "https://api.asaas.com/v3/customers";

            $payload = json_encode([
                "name"     => $b->name,
                "cpfCnpj"  => $b->cpf,
                "email"    => $b->email,
                "phone"    => $b->phone,
            ]);

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
                throw new Exception("Erro CURL ao criar customer Asaas: $error");
            }

            curl_close($ch);

            $data = json_decode($response, true);

            if ($httpCode >= 400) {
                throw new Exception("Erro Asaas ao criar cliente ($httpCode): " . $response);
            }

            if (!isset($data['id'])) {
                throw new Exception("Erro ao criar customer Asaas: resposta inválida");
            }

            return $data['id'];

        } catch (Exception $e) {
            throw $e;
        }
    }
}
