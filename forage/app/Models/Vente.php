<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = [
        'type_vente', 'client_id',
        'montant', 'statut_paiement', 'date_vente', 'notes', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_vente' => 'date',
            'montant'    => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function estCredit(): bool
    {
        return $this->statut_paiement === 'non_paye';
    }

    // Scopes
    public function scopeForage($query)
    {
        return $query->where('type_vente', 'forage');
    }

    public function scopeLivraison($query)
    {
        return $query->where('type_vente', 'livraison');
    }

    public function scopeExterieure($query)
    {
        return $query->where('type_vente', 'exterieure');
    }

    public function scopeCredits($query)
    {
        return $query->where('statut_paiement', 'non_paye');
    }

    public function scopeJournalier($query, $date = null)
    {
        return $query->whereDate('date_vente', $date ?? today());
    }

    public function scopeMensuel($query, $mois = null, $annee = null)
    {
        return $query->whereMonth('date_vente', $mois ?? now()->month)
                     ->whereYear('date_vente', $annee ?? now()->year);
    }

    public function scopeAnnuel($query, $annee = null)
    {
        return $query->whereYear('date_vente', $annee ?? now()->year);
    }
}
