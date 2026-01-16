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
		// SERVIÇOS
		Schema::create('convenios', function(Blueprint $table) {
            $table->increments('id');
			$table->string('nome_convenio');

            $table->unsignedInteger('partner_id'); // de qual parceiro pertence esse serviço
			$table->foreign('partner_id')->references('id')->on('partners')->onDelete('cascade');

            $table->text('descricao')->nullable();
            $table->decimal('desconto_percentual', 5, 2)->default(0);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo');
            $table->string('contato')->nullable();

			$table->unsignedInteger('convenio_categoria_id')->nullable();
            $table->foreign('convenio_categoria_id')
                  ->references('id')
                  ->on('convenios_categorias')
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
		Schema::drop('convenios');
	}
};
