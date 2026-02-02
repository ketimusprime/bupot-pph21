<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_cuts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('recipient_type'); // supplier or part_timer
            $table->unsignedBigInteger('recipient_id');
            $table->string('memo_number')->unique();
            $table->decimal('commission_amount', 15, 2);
            $table->decimal('dpp_amount', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(5);
            $table->decimal('tax_amount', 15, 2);
            $table->decimal('net_payment', 15, 2);
            $table->date('cut_date');
            $table->date('deposited_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['recipient_type', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_cuts');
    }
};
