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
		Schema::create('conta_pagars', function(Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('cost_center_id')->nullable();
			$table->foreign('cost_center_id')->references('id')->on('cost_centers');

			$table->string('centro_custo')->nullable();

			$table->enum('status_autorizacao', ['Aguardando', 'Aprovado', 'Recusado'])->default('Aguardando');
			$table->enum('status_pagamento', ['Pago', 'Não Pago'])->default('Não Pago');

			$table->unsignedInteger('partner_id');
			$table->foreign('partner_id')->references('id')
				->on('partners');

			// $table->unsignedBigInteger('autorizacao_compraid')->nullable();
			// FK para autorizacao_compras pode ser criada depois

			// FKs para usuários
			$table->unsignedInteger('usuario_exclusao_id')->nullable();
			$table->foreign('usuario_exclusao_id')->references('id')->on('users');

			$table->unsignedInteger('usuario_baixa_id')->nullable();
			$table->foreign('usuario_baixa_id')->references('id')->on('users');

			$table->unsignedInteger('usuario_cadastro_id');
			$table->foreign('usuario_cadastro_id')->references('id')->on('users');

			$table->unsignedInteger('usuario_atualizacao_id')->nullable();
			$table->foreign('usuario_atualizacao_id')->references('id')->on('users');

			$table->dateTime('cadastro')->nullable();
			$table->dateTime('exclusao')->nullable();
			$table->dateTime('atualizacao')->nullable();
			$table->date('emissao')->nullable();
			$table->date('vencimento')->nullable();
			$table->date('baixa')->nullable();
			$table->date('pagamento')->nullable();

			$table->string('documento')->nullable();
			$table->decimal('valor', 15, 2)->nullable();
			$table->decimal('valor_pago', 15, 2)->nullable();
			$table->decimal('valor_desconto', 15, 2)->nullable();
			$table->decimal('juros', 15, 2)->nullable();
			$table->decimal('multa', 15, 2)->nullable();
			$table->text('obs')->nullable();
			$table->string('tipo_juros')->nullable();
			$table->decimal('valor_pago_juros_multa', 15, 2)->nullable();
			$table->string('tipo_baixa', 2)->nullable();

			$table->unsignedInteger('caixa_id')->nullable();
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
		Schema::drop('conta_pagars');
	}
};
