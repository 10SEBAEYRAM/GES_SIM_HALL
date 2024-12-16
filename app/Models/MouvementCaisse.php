<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementCaisse extends Model
{
    use HasFactory;

    protected $table = 'mouvements_caisse';

    protected $fillable = [
        'caisse_id',
        'type_mouvement',
        'montant',
        'motif',
        'solde_avant',
        'solde_apres',
        'user_id'
    ];

    // Cast des attributs
    protected $casts = [
        'montant' => 'decimal:2',
        'solde_avant' => 'decimal:2',
        'solde_apres' => 'decimal:2',
    ];

    // Relation avec la caisse
    public function caisse()
    {
        return $this->belongsTo(Caisse::class, 'caisse_id', 'id_caisse');
    }

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_util');
    }

    // Accesseur pour formater le type de mouvement
    public function getTypeFormateAttribute()
    {
        $types = [
            'emprunt' => 'Emprunt',
            'remboursement' => 'Remboursement',
            'retrait' => 'Retrait'
        ];

        return $types[$this->type_mouvement] ?? $this->type_mouvement;
    }

    // Accesseur pour obtenir la différence de solde
    public function getDifferenceAttribute()
    {
        return $this->solde_apres - $this->solde_avant;
    }
}
