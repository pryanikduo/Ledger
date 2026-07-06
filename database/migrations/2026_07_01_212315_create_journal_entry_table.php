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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->bigIncrements('journal_id');
            $table->unsignedBigInteger('transaction_id')->index('transaction_idx');
            $table->unsignedBigInteger('account_id')->index('account_idx');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['debit', 'credit'])->index('type_idx');

            $table->foreign(['transaction_id'], 'journal_entries_fk1')->references(['transaction_id'])
                ->on('transactions')->onDelete('cascade');
            $table->foreign(['account_id'], 'journal_entries_fk2')->references(['account_id'])
                ->on('accounts')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
