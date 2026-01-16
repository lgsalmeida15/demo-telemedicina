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
		Schema::create('cost_centers', function(Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('usuario_id')->nullable();
			$table->foreign('usuario_id')
				->references('id')
				->on('users');

			$table->timestamp('cadastro')->nullable();
			$table->timestamp('exclusao')->nullable();
			$table->timestamp('atualizacao')->nullable();
			$table->string('codigo_reduzido')->nullable();
			$table->string('codigo_conta')->nullable();
			$table->string('descricao')->nullable();
			$table->enum('tipo', ['D', 'C']); 
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cost_centers');
	}
};
