<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE journal_activites MODIFY COLUMN type_action ENUM('vente','paiement','credit','depense','client','connexion','ardoise','autre') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE journal_activites MODIFY COLUMN type_action ENUM('vente','paiement','credit','depense','client','connexion','autre') NOT NULL");
    }
};
