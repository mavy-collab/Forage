<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ardoise_forages', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->decimal('montant', 10, 2);
            $table->date('date_ardoise');
            $table->boolean('rembourse')->default(false);
            $table->date('date_remboursement')->nullable();
            $table->string('notes', 300)->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ardoise_forages');
    }
};
