<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalActivite extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'type_action', 'description', 'donnees'];

    protected function casts(): array
    {
        return [
            'donnees' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function enregistrer(string $type, string $description, array $donnees = [], ?int $userId = null): void
    {
        static::create([
            'user_id' => $userId ?? auth()->id(),
            'type_action' => $type,
            'description' => $description,
            'donnees' => $donnees ?: null,
        ]);
    }
}
