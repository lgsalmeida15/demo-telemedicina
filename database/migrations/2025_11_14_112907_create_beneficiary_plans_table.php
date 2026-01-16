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
		// tabela feita para resolver o relacionamento many to many entre beneficiarios e planos
		Schema::create('beneficiary_plans', function(Blueprint $table) {
            $table->increments('id');

			// relacionamento com beneficiarios
			$table->unsignedInteger('beneficiary_id');
			$table->foreign('beneficiary_id')->references('id')->on('beneficiaries')
			->onDelete('cascade');

			// relacionamento com planos
			$table->unsignedInteger('plan_id');
			$table->foreign('plan_id')->references('id')->on('plans')
			->onDelete('cascade');

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
		Schema::drop('beneficiary_plans');
	}
};
