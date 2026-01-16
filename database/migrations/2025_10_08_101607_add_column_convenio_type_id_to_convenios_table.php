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
        Schema::table('convenios', function (Blueprint $table) {
            $table->unsignedInteger('convenio_type_id')->nullable()->after('convenio_categoria_id');
            $table->foreign('convenio_type_id')->references('id')->on('convenio_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropForeign('convenios_convenio_type_id_foreign');
            $table->dropColumn('convenio_type_id');
        });
    }
};
