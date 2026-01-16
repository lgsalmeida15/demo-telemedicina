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
		Schema::create('caixas', function(Blueprint $table) {
            $table->increments('id');

			$table->string('descricao');
			$table->text('obs')->nullable();

			$table->date('cadastro')->nullable();
			$table->date('atualizacao')->nullable();
			$table->date('exclusao')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('caixas');
	}
};
