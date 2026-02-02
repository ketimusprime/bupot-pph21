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
        Schema::table('tax_cuts', function (Blueprint $table) {
            //
            $table->enum('pph_method', ['gross', 'gross_up'])->default('gross');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_cuts', function (Blueprint $table) {
            //
        });
    }
};
