<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = ['vente_id', 'montant', 'date_paiement', 'notes', 'user_id'];

    protected function casts(): array
    {
        return [
            'date_paiement' => 'date',
            'montant' => 'decimal:2',
        ];
    }

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
