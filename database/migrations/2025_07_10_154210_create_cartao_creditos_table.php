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
		Schema::create('cartao_creditos', function(Blueprint $table) {
            $table->increments('id');
			$table->string('numero_cartao', 20); // armazene mascarado!
            $table->string('nome_portador');
            $table->string('bandeira', 50)->nullable();
            $table->decimal('limite', 10, 2)->default(0);
            $table->decimal('limite_disponivel', 10, 2)->default(0);
            $table->date('data_vencimento')->nullable();
            $table->enum('status', ['Ativo', 'Bloqueado', 'Cancelado'])
                  ->default('Ativo');
            $table->text('observacoes')->nullable();
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
		Schema::drop('cartao_creditos');
	}
};
