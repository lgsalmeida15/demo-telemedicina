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
        // Cria a tabela 'companies' (empresas)
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid')->unique(); 
            $table->string('name'); // nome
            $table->string('cnpj')->unique(); // cnpj, único
            $table->string('email')->nullable(); // e-mail, pode ser nulo
            $table->string('phone')->nullable(); // telefone, pode ser nulo
            $table->timestamps(); // created_at e updated_at
            $table->softDeletes(); // deleted_at para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a tabela 'companies'
        Schema::dropIfExists('companies');
    }
};