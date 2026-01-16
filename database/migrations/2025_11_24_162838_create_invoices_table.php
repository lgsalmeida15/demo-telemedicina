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
		/**
		 * Tabela de faturas do beneficiário, vinculadas tbm ao plano contratado
		 */
		Schema::create('invoices', function(Blueprint $table) {
            $table->increments('id');

			$table->uuid('uuid')->unique();

			$table->unsignedInteger('beneficiary_plan_id');
			$table->foreign('beneficiary_plan_id')->references('id')->on('beneficiary_plans')->onDelete('cascade');

			$table->unsignedInteger('beneficiary_id');
			$table->foreign('beneficiary_id')->references('id')->on('beneficiaries')->onDelete('cascade');

			// $table->unsignedInteger('asaas_customer_id'); // id do cliente no Asaas
			$table->string('asaas_payment_id'); // id do pagamento no Asaas

			$table->integer('competence_month'); // mês de competencia
			$table->integer('competence_year'); // ano de competencia[
			$table->double('invoice_value', 10, 2); // valor da fatura
			$table->string('status'); // A - Aguardando, P - Pago, C - Cancelado, V - Vencido
			$table->date('due_date'); // data de vencimento
			$table->string('payment_type', 50); // tipo de pagamento
			$table->date('payment_date')->nullable(); // data de pagamento
			
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('invoices');
	}
};
