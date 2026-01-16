<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * Remover coluna e fk plan id da tabela de beneficiaries
         */
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->unsignedInteger('plan_id')->nullable()->after('company_id');
            $table->foreign('plan_id')
                ->references('id')->on('plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
