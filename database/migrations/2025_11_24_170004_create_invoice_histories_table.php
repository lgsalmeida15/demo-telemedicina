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
		Schema::create('invoice_histories', function(Blueprint $table) {
            $table->increments('id');

			$table->uuid('uuid')->unique();

			$table->unsignedInteger('invoice_id');
			$table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');

			$table->longText('transaction_code'); // código de transação
			$table->string('status_transaction'); // P - Pago, C - Cancelado, A - Aguardando
			$table->string('return_code')->nullable(); // código de retorno
			$table->longText('return_message')->nullable(); // mensagem de retorno

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
		Schema::drop('invoice_histories');
	}
};
