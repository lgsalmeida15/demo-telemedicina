<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cria a tabela 'beneficiaries' (beneficiários)
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->increments('id');
            
            // Chave estrangeira que conecta 'beneficiaries' a 'companies'
            $table->unsignedInteger('company_id');
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Chave estrangeira para a tabela de planos 
            $table->unsignedInteger('plan_id');
            $table->foreign('plan_id')
                ->references('id')->on('plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('name'); // NOME DO COLABORADOR
            $table->string('cpf')->unique(); // CPF
            $table->string('email')->nullable(); // e-mail
            $table->string('phone')->nullable(); // contato
            $table->enum('action', ['I', 'E', 'M'])->default('I')->comment('I: Inclusion, E: Exclusion, M: Maintenance'); // Coluna A: AÇÃO (I / E / M)
            $table->date('birth_date')->nullable(); // Coluna C: NASCIMENTO
            $table->enum('gender', ['M', 'F'])->nullable(); // Coluna E: SEXO (M ou F)
            $table->string('relationship')->nullable(); // Coluna G: VÍNCULO (Grau de parentesco ou tipo de dependência)
            $table->string('mother_name')->nullable(); // Coluna H: NOME DA MÃE
            // Campos de detalhe solicitados (Controle)
            $table->date('inclusion_date')->nullable(); // Data em que o beneficiário foi incluído
            $table->date('exclusion_date')->nullable(); // Data em que o beneficiário foi excluído (se aplicável)

            $table->timestamps(); // created_at e updated_at
            $table->softDeletes(); // deleted_at para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a tabela 'beneficiaries'
        Schema::dropIfExists('beneficiaries');
    }
};

