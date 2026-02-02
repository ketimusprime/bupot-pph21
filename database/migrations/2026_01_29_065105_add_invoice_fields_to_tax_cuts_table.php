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
            $table->string('invoice_number')->nullable()->after('memo_number');
            $table->date('invoice_date')->nullable()->after('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_cuts', function (Blueprint $table) {
            //
            $table->dropColumn(['invoice_number', 'invoice_date']);
        });
    }
};
