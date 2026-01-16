<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('conta_recebers', function(Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('plano_contas_id')->nullable(); // plano_contas_codigo
            $table->unsignedInteger('usuario_baixa_id')->nullable(); // usuario_baixa_codigo
            $table->unsignedInteger('usuario_atualizacao_id')->nullable(); // usuario_atualizacao_codigo
            $table->unsignedInteger('usuario_exclusao_id')->nullable(); // usuario_exclusao_codigo
            $table->unsignedInteger('usuario_cadastro_id')->nullable(); // usuario_cadastro_codigo
            $table->unsignedInteger('caixa_id')->nullable(); // caixa_codigo

            // Datas
            $table->timestamp('cadastro')->nullable(); // cadastro
            $table->timestamp('exclusao')->nullable(); // exclusao
            $table->timestamp('atualizacao')->nullable(); // atualizacao
            $table->date('emissao')->nullable();
            $table->date('vencimento')->nullable();
            $table->timestamp('baixa')->nullable();
            $table->date('pagamento')->nullable();

            // Dados financeiros e documentos
            $table->string('documento')->nullable();
            $table->decimal('valor', 15, 3);
            $table->decimal('valor_pago', 15, 3)->nullable();
            $table->decimal('valor_desconto', 15, 3)->nullable();
            $table->decimal('juros', 15, 3)->nullable();
            $table->decimal('multa', 15, 3)->nullable();
            $table->text('obs')->nullable();
            $table->string('tipo_juros')->nullable();
            $table->decimal('valor_pago_juros_multa', 15, 3)->nullable();
            $table->string('tipo_baixa', 2)->nullable();

            // Status
            $table->enum('status_pagamento', ['Pago', 'Não Pago'])->default('Não Pago');
            $table->enum('status_autorizacao', ['Aguardando', 'Aprovado', 'Recusado'])->default('Aguardando');

            // FKs
            $table->foreign('plano_contas_id')->references('id')->on('cost_centers');
            $table->foreign('usuario_baixa_id')->references('id')->on('users');
            $table->foreign('usuario_atualizacao_id')->references('id')->on('users');
            $table->foreign('usuario_exclusao_id')->references('id')->on('users');
            $table->foreign('usuario_cadastro_id')->references('id')->on('users');
            $table->foreign('caixa_id')->references('id')->on('caixas');
            
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('conta_recebers');
	}
};
