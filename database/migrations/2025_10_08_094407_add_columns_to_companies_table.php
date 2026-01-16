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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('uf')->nullable()->after('phone'); // UF: PA, AM, etc...
            $table->date('billing_date')->nullable()->after('uf'); // data de faturamento
            $table->integer('due_day')->nullable()->after('billing_date'); // dia de vencimento (ex: todo dia 20)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(
                [
                    'uf',
                    'billing_date',
                    'due_day'
                ]
            );
        });
    }
};
