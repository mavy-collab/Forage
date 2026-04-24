<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['nom', 'adresse', 'contact'];

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function ventesNonPayees()
    {
        return $this->hasMany(Vente::class)->where('statut_paiement', 'non_paye');
    }

    public function totalDette(): float
    {
        return $this->ventesNonPayees()->sum('montant');
    }
}
