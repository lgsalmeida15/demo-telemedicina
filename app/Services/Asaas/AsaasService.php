<?php

namespace App\Services\Asaas;

use Illuminate\Support\Facades\Http;
use Exception;

class AsaasService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.asaas.url');
        $this->apiKey = config('services.asaas.key');
    }

    protected function request(string $method, string $endpoint, array $params = [])
    {
        $response = Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])->{$method}($this->baseUrl . $endpoint, $params);

        if (!$response->successful()) {
            throw new Exception(
                'Erro Asaas: ' . $response->body()
            );
        }

        return $response->json();
    }

    public function getCustomers(int $offset = 0, int $limit = 100)
    {
        return $this->request('get', '/customers', [
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    public function getPayments(array $params = [])
    {
        return $this->request('get', '/payments', $params);
    }


    public function getSubscriptions(int $offset = 0, int $limit = 100)
    {
        return $this->request('get', '/subscriptions', [
            'offset' => $offset,
            'limit' => $limit
        ]);
    }

    /**
     * Summary of updateSubscriptionCreditCard
     * @param string $subscriptionId
     * @param array $creditCard
     * @param array $holderInfo
     * @param string $remoteIp
     */
    public function updateSubscriptionCreditCard(
        string $subscriptionId,
        array $creditCard,
        array $holderInfo,
        string $remoteIp
    ) {
        return $this->request(
            'put',
            "/subscriptions/{$subscriptionId}/creditCard",
            [
                'creditCard' => [
                    'holderName' => $creditCard['holderName'],
                    'number' => $creditCard['number'],
                    'expiryMonth' => $creditCard['expiryMonth'],
                    'expiryYear' => $creditCard['expiryYear'],
                    'ccv' => $creditCard['ccv'],
                ],
                'creditCardHolderInfo' => [
                    'name' => $holderInfo['name'],
                    'email' => $holderInfo['email'],
                    'cpfCnpj' => $holderInfo['cpfCnpj'],
                    'postalCode' => $holderInfo['postalCode'],
                    'addressNumber' => $holderInfo['addressNumber'],
                    'addressComplement' => $holderInfo['addressComplement'] ?? null,
                    'phone' => $holderInfo['phone'] ?? null,
                    'mobilePhone' => $holderInfo['mobilePhone'] ?? null,
                ],
                'remoteIp' => $remoteIp
            ]
        );
    }


    // =========================
    // CANCELAR ASSINATURA
    // =========================
    public function cancelSubscription(string $subscriptionId)
    {
        return $this->request(
            'delete',
            "/subscriptions/{$subscriptionId}"
        );
    }

    // =========================
    // GERAR LINK DO PORTAL DO CLIENTE (ATUALIZAR CARTÃO)
    // =========================
    public function generateCustomerPortalLink(string $asaasCustomerId)
    {
        return $this->request(
            'post',
            '/customerPortal',
            [
                'customer' => $asaasCustomerId
            ]
        );
    }

}
