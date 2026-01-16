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
		// Tabela feita para resolver a relação many to many entre empresas e serviços (antiga convenios table)
		Schema::create('plan_convenios', function(Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('plan_id'); // fk planos
			$table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');

			$table->unsignedInteger('convenio_id'); // fk convenios
			$table->foreign('convenio_id')->references('id')->on('convenios')->onDelete('cascade');

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
		Schema::drop('plan_convenios');
	}
};
