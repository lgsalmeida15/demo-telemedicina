<?php

namespace App\Imports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows; // Para pular linhas completamente vazias
use Carbon\Carbon;
use Illuminate\Validation\Rule;

// Implementamos SkipsEmptyRows
class BeneficiaryImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $companyId;
    private $planId;
    
    // Define que a 10ª linha é a linha dos cabeçalhos, conforme sua planilha de exemplo.
    public function headingRow(): int
    {
        return 10;
    }

    public function __construct(int $companyId, int $planId)
    {
        $this->companyId = $companyId;
        $this->planId = $planId;
    }

    /**
    * Mapeia cada linha da planilha para um modelo.
    *
    * @param array $row
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Limpeza e Validação de Dados Chave
        $cpfLimpo = preg_replace('/[^0-9]/', '', $row['cpf'] ?? '');
        
        // Se 'acao' estiver vazia, assume 'I' (Inclusão) por padrão.
        $action = strtoupper($row['acao'] ?? 'I');

        // Ignora linhas que não têm CPF E não têm NOME.
        if (empty($cpfLimpo) || empty($row['nome'])) {
             return null; 
        }

        // 2. Funções Auxiliares de Parsing
        $parseDate = function($value) {
            // Se for um número (formato de data Excel)
            if (is_numeric($value) && $value > 0) {
                // Tenta converter o serial date do Excel
                try {
                     return Carbon::createFromTimestamp(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value));
                } catch (\Exception $e) {
                     // Ignora erro de serial date inválido
                }
            }
            // Se for uma string (formato de data normal)
            try {
                // Garante que é uma string antes de tentar o parse
                return is_string($value) && $value ? Carbon::parse($value) : null;
            } catch (\Exception $e) {
                return null;
            }
        };

        // 3. Busca o Beneficiário Existente
        $beneficiary = Beneficiary::where('cpf', $cpfLimpo)
                                   ->where('company_id', $this->companyId)
                                   ->first();

        // 4. LÓGICA DO SEXO: Se o valor lido não for 'M' ou 'F', ele será definido como null.
        $gender = trim(strtoupper($row['sexo'] ?? ''));
        $gender = in_array($gender, ['M', 'F']) ? $gender : null;

        // 5. Trata o VALOR: Agora confiamos apenas nesta lógica para definir o valor numérico.
        // Se o valor não for reconhecido como numérico (incluindo strings, R$, etc.), ele vira 0.00.
        $value = is_numeric($row['valor'] ?? 0.00) ? (float)$row['valor'] : 0.00;

        // 6. Prepara os Dados
        $data = [
            'company_id' => $this->companyId,
            'plan_id' => $this->planId,
            'name' => $row['nome'],
            'cpf' => $cpfLimpo,
            'mother_name' => $row['nome_da_mae'] ?? null,
            'birth_date' => $parseDate($row['nascimento'] ?? null),
            'gender' => $gender, // Usa o valor limpo (M, F ou null)
            'value' => $value, // CORREÇÃO: Usando a variável $value limpa
            // O campo 'vinculo' é a relação, mas a planilha pode ter uma data aqui.
            'relationship' => (is_string($row['vinculo'] ?? null) && !empty($row['vinculo'])) ? $row['vinculo'] : null, 
            'action' => $action,
            // A data de inclusão será a data válida em 'vinculo' OU a data atual
            'inclusion_date' => $parseDate($row['vinculo'] ?? null) ?? now(), 
        ];

        // 7. Lógica de Persistência (I / M / E)
        if ($action === 'E') {
            if ($beneficiary) {
                $beneficiary->delete();
            }
            return null;

        } elseif ($action === 'I' || $action === 'M') {
            
            if ($beneficiary) {
                $updateData = array_filter($data, fn($value) => !is_null($value) && $value !== '');
                $beneficiary->update(array_merge($updateData, ['action' => 'M']));
                return null;
            } else {
                return new Beneficiary($data);
            }
        }
        
        return null; // Linha com ação inválida é ignorada
    }

    /**
     * Regras de Validação de Linha
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // Acao é nullable agora, permitindo que a linha passe e seja padronizada para 'I' no model()
            'acao' => ['nullable', 'string', Rule::in(['I', 'M', 'E', 'i', 'm', 'e'])], 
            
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|min:11', 
            'nascimento' => 'nullable', 
            // Vínculo aceita número (data Excel) ou string
            'vinculo' => 'nullable|max:100', 
            
            // CORREÇÃO FINAL: Removendo 'numeric' da validação. 
            // Confiamos na limpeza do método model() para lidar com caracteres não numéricos.
            'valor' => 'nullable', 
            
            'sexo' => 'nullable', 
        ];
    }
}
