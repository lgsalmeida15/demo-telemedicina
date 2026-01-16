<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            $table->text('docway_dependent_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('dependents', function (Blueprint $table) {
            $table->unsignedInteger('docway_dependent_id')
                ->nullable()
                ->change();
        });
    }
};
