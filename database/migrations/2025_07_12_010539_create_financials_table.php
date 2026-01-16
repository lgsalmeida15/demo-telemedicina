<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
      /**
       * Run the migrations.
       *
       * @return void
       */
      public function up()
      {
            Schema::create('financials', function (Blueprint $table) {
                  $table->increments('id');

                  $table->dateTime('data_hora_evento');
                  $table->enum('tipo', ['entrada', 'saida']);
                  $table->string('descricao', 255);
                  $table->decimal('valor', 15, 2);

                  // FK centro de custo (snake_case minúsculo)
                  $table->unsignedInteger('cost_center_id');
                  $table->foreign('cost_center_id')
                        ->references('id')->on('cost_centers')
                        ->restrictOnDelete()
                        ->cascadeOnUpdate();

                  // FK usuário (opcional)
                  $table->unsignedInteger('user_id')->nullable();
                  $table->foreign('user_id')
                        ->references('id')->on('users')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();

                  $table->unsignedInteger('caixa_id')->nullable();
                  $table->foreign('caixa_id')
                        ->references('id')->on('caixas')
                        ->nullOnDelete()
                        ->cascadeOnUpdate();

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
            Schema::drop('financials');
      }
};
