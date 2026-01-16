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
		// !!!
		Schema::create('plans', function(Blueprint $table) {
            $table->increments('id');

			$table->unsignedInteger('company_id'); // Relacionamento com empresas
			$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

			$table->string('name'); 
            $table->decimal('value', 8, 2); 
            $table->text('description')->nullable(); 

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
		Schema::drop('plans');
	}
};
