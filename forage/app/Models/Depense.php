<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    protected $fillable = ['montant', 'motif', 'date_depense', 'user_id'];

    protected function casts(): array
    {
        return [
            'date_depense' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMensuel($query, $mois = null, $annee = null)
    {
        return $query->whereMonth('date_depense', $mois ?? now()->month)
                     ->whereYear('date_depense', $annee ?? now()->year);
    }

    public function scopeAnnuel($query, $annee = null)
    {
        return $query->whereYear('date_depense', $annee ?? now()->year);
    }
}
