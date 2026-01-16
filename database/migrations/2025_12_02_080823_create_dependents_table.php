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
		Schema::create('dependents', function(Blueprint $table) {
            $table->increments('id');
			$table->unsignedInteger('docway_dependent_id')->nullable();

			$table->unsignedInteger('beneficiary_id');
			$table->foreign('beneficiary_id')->references('id')
			->on('beneficiaries')
			->onDelete('cascade');

			$table->string('name');
			$table->date('birth_date')->nullable();
			$table->string('gender')->nullable();
			$table->string('cpf')->unique()->nullable();
			$table->string('relationship')->nullable();

			$table->dateTime('deleted_at')->nullable();
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
		Schema::drop('dependents');
	}
};
