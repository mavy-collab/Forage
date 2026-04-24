<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->enum('type_vente', ['forage', 'livraison', 'exterieure']);
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->decimal('quantite', 10, 2);
            $table->string('unite')->default('litre'); // litre, bidon, fut
            $table->decimal('montant', 10, 2);
            $table->enum('statut_paiement', ['paye', 'non_paye'])->default('paye');
            $table->date('date_vente');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
