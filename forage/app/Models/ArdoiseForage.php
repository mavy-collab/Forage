<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArdoiseForage extends Model
{
    protected $fillable = ['nom', 'montant', 'date_ardoise', 'rembourse', 'date_remboursement', 'notes', 'user_id'];

    protected function casts(): array
    {
        return [
            'date_ardoise'        => 'date',
            'date_remboursement'  => 'date',
            'rembourse'           => 'boolean',
            'montant'             => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEnCours($query)
    {
        return $query->where('rembourse', false);
    }

    public function scopeRemboursees($query)
    {
        return $query->where('rembourse', true);
    }
}
