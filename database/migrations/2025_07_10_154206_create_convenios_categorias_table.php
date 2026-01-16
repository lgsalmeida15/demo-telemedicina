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
		Schema::create('convenios_categorias', function(Blueprint $table) {
            $table->increments('id');
			$table->string('nome');
			$table->text('descricao')->nullable();

			$table->unsignedInteger('usuario_atualizacao_id')->nullable(); // usuario_atualizacao_codigo
            $table->unsignedInteger('usuario_exclusao_id')->nullable(); // usuario_exclusao_codigo
            $table->unsignedInteger('usuario_cadastro_id')->nullable();

			$table->date('cadastro')->nullable();
			$table->date('exclusao')->nullable();
			$table->date('atualizacao')->nullable();

			// FKs
			$table->foreign('usuario_atualizacao_id')->references('id')->on('users');
            $table->foreign('usuario_exclusao_id')->references('id')->on('users');
            $table->foreign('usuario_cadastro_id')->references('id')->on('users');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('convenios_categorias');
	}
};
