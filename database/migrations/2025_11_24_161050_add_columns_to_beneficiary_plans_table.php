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
         * Add de colunas na tabela pivô 'beneficiary_plans'
         */
        Schema::table('beneficiary_plans', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('plan_id'); // data inicial
            $table->date('end_date')->nullable()->after('start_date'); // data final 
            $table->longText('transaction_code')->nullable()->after('end_date'); // código de transação
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_plans', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'start_date',
                    'end_date',
                    'transaction_code'
                ]
            );
        });
    }
};
