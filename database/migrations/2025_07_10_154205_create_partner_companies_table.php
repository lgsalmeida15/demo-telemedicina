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
		// Tabela de Indicações de empresas por parceiros
		Schema::create('partner_companies', function(Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('partner_id');
			$table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');

			$table->unsignedInteger('company_id');
			$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

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
		Schema::drop('partner_companies');
	}
};
