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
		 * Tipos de serviço
		 */
		Schema::create('convenio_types', function(Blueprint $table) {
            $table->increments('id');

			$table->string('name'); // nome do tipo de serviço (seguro, app, odonto...)

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
		Schema::drop('convenio_types');
	}
};
