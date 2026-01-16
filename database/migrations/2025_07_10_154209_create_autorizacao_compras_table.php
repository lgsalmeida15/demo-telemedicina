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
		Schema::create('autorizacao_compras', function(Blueprint $table) {
            $table->increments('id');
			$table->string('numero_autorizacao', 50)->unique();
            // Solicitante
            $table->unsignedInteger('SOLICITANTE_ID');
            $table->foreign('SOLICITANTE_ID')
                  ->references('id')->on('beneficiaries')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Partner
            $table->unsignedInteger('FORNECEDOR_ID');
            $table->foreign('FORNECEDOR_ID')
                  ->references('id')->on('partners')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Autorizador (opcional)
            $table->unsignedInteger('AUTORIZADOR_ID')->nullable();
            $table->foreign('AUTORIZADOR_ID')
                  ->references('id')->on('beneficiaries')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->date('data_solicitacao');
            $table->date('data_autorizacao')->nullable();
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->text('descricao')->nullable();
            $table->enum('status', ['Pendente', 'Autorizado', 'Rejeitado'])
                  ->default('Pendente');
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
		Schema::drop('autorizacao_compras');
	}
};
