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
		Schema::create('partners', function(Blueprint $table) {
            
			$table->increments('id'); // Primary key, auto-incrementing

            $table->string('name'); // nome
            $table->string('cnpj')->unique(); // cnpj, único (Parceiros sempre serão PJ)
            $table->text('description')->nullable(); // descrição, pode ser nula
            $table->text('email')->nullable(); // descrição, pode ser nula
            $table->text('phone')->nullable(); // descrição, pode ser nula
            $table->unsignedInteger('cost_center_id')->nullable(); // Foreign key para cost_centers, pode ser nula
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->onDelete('set null');
            $table->timestamps(); // created_at e updated_at
            $table->softDeletes(); // deleted_at para soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a tabela 'partners'
        Schema::dropIfExists('partners');
    }
};