<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class IBAMService
{
    private $baseUrl;
    private $uuid;
    private $token;

    public function __construct($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        // UUID sempre vem do .env
        $this->uuid = env('IBAM_UUID');

    }

    /**
     * -------------------------------------------------------------------------
     *  FUNÇÃO GENÉRICA DE REQUEST
     * -------------------------------------------------------------------------
     */
    private function request($method, $endpoint, $payload = null)
    {
        $url = $this->baseUrl . $endpoint;
        
        // Prepara headers
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        
        if ($this->token) {
            $headers['Authorization'] = "Bearer {$this->token}";
        }

        // Faz requisição usando Laravel HTTP Client (Guzzle)
        try {
            $http = Http::withHeaders($headers)
                ->withoutVerifying(); // equivalente a CURLOPT_SSL_VERIFYPEER => false

            $methodUpper = strtoupper($method);
            
            if ($methodUpper === 'GET') {
                $response = $http->get($url);
            } elseif ($methodUpper === 'POST') {
                $response = $http->post($url, $payload ?? []);
            } elseif ($methodUpper === 'PUT') {
                $response = $http->put($url, $payload ?? []);
            } elseif ($methodUpper === 'DELETE') {
                $response = $http->delete($url, $payload ?? []);
            } else {
                throw new Exception("Método HTTP não suportado: {$method}");
            }

            return [
                "status" => $response->status(),
                "response" => $response->json()
            ];
        } catch (\Exception $e) {
            throw new Exception("Erro na requisição IBAM: " . $e->getMessage());
        }
    }

    /**
     * -------------------------------------------------------------------------
     *  LOGIN
     * -------------------------------------------------------------------------
     */
    public function login()
    {
        $this->uuid = env('IBAM_UUID');
        $payload = ["uuid" => env('IBAM_UUID')];
        $result = $this->request("POST", "/auth/login", $payload);
        if (!isset($result["response"]["token"])) {
            throw new Exception("Falha no login IBAM: token inexistente");
        }
        $this->token = $result["response"]["token"];
        return $result["response"];
    }

    /**
     * -------------------------------------------------------------------------
     *  BENEFICIÁRIO – Criar
     * -------------------------------------------------------------------------
     */
    public function createBeneficiary(array $data)
    {
        return $this->request("POST", "/beneficiary/create", $data);
    }

    /**
     * -------------------------------------------------------------------------
     *  BENEFICIÁRIO – Atualizar
     * -------------------------------------------------------------------------
     */
    public function updateBeneficiary($uuidDocway, array $data)
    {
        return $this->request("PUT", "/beneficiary/{$uuidDocway}/update", $data);
    }

    /**
     * -------------------------------------------------------------------------
     *  BENEFICIÁRIO – Deletar
     * -------------------------------------------------------------------------
     */
    public function deleteBeneficiary($uuidDocway)
    {
        return $this->request("DELETE", "/beneficiary/{$uuidDocway}/delete");
    }

    /**
     * -------------------------------------------------------------------------
     *  BENEFICIÁRIO – Buscar CPF ou termo
     * -------------------------------------------------------------------------
     */
    public function findBeneficiary($cpf = null, $term = null)
    {
        $query = [];

        if ($cpf) $query["cpf"] = preg_replace('/\D/','',$cpf);
        if ($term) $query["term"] = $term;

        $queryString = http_build_query($query);

        return $this->request("GET", "/beneficiary/find?" . $queryString);
    }

    /**
     * -------------------------------------------------------------------------
     *  DEPENDENTE – Criar
     * -------------------------------------------------------------------------
     * POST /beneficiary/{cpf}/dependent/create
     */
    public function createDependent(string $cpf, array $data)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        return $this->request(
            "POST",
            "/beneficiary/{$cpf}/dependent/create",
            $data
        );
    }

    /**
     * -------------------------------------------------------------------------
     *  DEPENDENTE – Listar
     * -------------------------------------------------------------------------
     * GET /beneficiary/{cpf}/dependent/list
     */
    public function listDependents(string $cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        return $this->request(
            "GET",
            "/beneficiary/{$cpf}/dependent/list"
        );
    }

    /**
     * -------------------------------------------------------------------------
     *  DEPENDENTE – Apagar
     * -------------------------------------------------------------------------
     * DELETE /beneficiary/{cpf}/dependent/delete
     */
    public function deleteDependent(string $cpf, string $dependentCpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        $dependentCpf = preg_replace('/\D/', '', $dependentCpf);

        return $this->request(
            "DELETE",
            "/beneficiary/{$cpf}/dependent/delete",
            [
                "cpf" => $dependentCpf
            ]
        );
    }


    /**
     * -------------------------------------------------------------------------
     *  ATENDIMENTO – Iniciar
     * -------------------------------------------------------------------------
     */
    public function medcareCreate($docwayPatientId, array $data)
    {
        return $this->request("POST", "/beneficiary/{$docwayPatientId}/medcare/create", $data);
    }
    
    /**
     * -------------------------------------------------------------------------
     *  ATENDIMENTO – Lista de Horários Disponíveis
     * -------------------------------------------------------------------------
     *
     * GET /beneficiary/{docway_patient_id}/medcare/specialties/{specialtyId}/hours?date=YYYY-MM-DD
     */
    public function medcareAvailableHours($docwayPatientId, $specialtyId, $date)
    {
        $query = http_build_query([
            "date" => $date
        ]);
        return $this->request(
            "GET",
            "/beneficiary/{$docwayPatientId}/medcare/specialties/{$specialtyId}/hours?{$query}"
        );
    }


    /**
     * -------------------------------------------------------------------------
     *  ATENDIMENTO – Listar
     * -------------------------------------------------------------------------
     */
    public function medcareList($docwayPatientId)
    {
        return $this->request("GET", "/beneficiary/{$docwayPatientId}/medcare/list");
    }

    /**
     * -------------------------------------------------------------------------
     *  ATENDIMENTO – Listar Dependentes
     * -------------------------------------------------------------------------
     */
    public function medcareListDependent($cpf)
    {
        return $this->request("GET", "/beneficiary/{$cpf}/dependent/medcare/list");
    }

    /**
     * -------------------------------------------------------------------------
     *  ATENDIMENTO – Info
     * -------------------------------------------------------------------------
     */
    public function medcareInfo($docwayPatientId, $idAtendimento)
    {
        return $this->request("GET", "/beneficiary/{$docwayPatientId}/medcare/{$idAtendimento}/info");
    }

    /**
     * -------------------------------------------------------------------------
     *  ATENDIMENTO – Cancelar
     * -------------------------------------------------------------------------
     */
    public function medcareCancel($docwayPatientId, $idAtendimento)
    {
        return $this->request("DELETE", "/beneficiary/{$docwayPatientId}/medcare/{$idAtendimento}/cancel");
    }
}
