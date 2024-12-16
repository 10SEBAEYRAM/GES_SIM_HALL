<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    use HasFactory;

    protected $table = 'caisses';
    protected $primaryKey = 'id_caisse';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nom_caisse',
        'balance_caisse',
        'total_emprunts',
        'total_remboursements',
        'total_retraits'
    ];

    protected $casts = [
        'balance_caisse' => 'decimal:2',
        'total_emprunts' => 'decimal:2',
        'total_remboursements' => 'decimal:2',
        'total_retraits' => 'decimal:2'
    ];

    // Relation avec les mouvements de caisse
    public function mouvements()
    {
        return $this->hasMany(MouvementCaisse::class, 'caisse_id', 'id_caisse');
    }

    // Méthodes utilitaires
    public function getSoldeDisponibleAttribute()
    {
        return $this->balance_caisse;
    }

    public function getMontantEmprunteAttribute()
    {
        return $this->emprunt_sim_hall - $this->remboursement_sim_hall;
    }

    public function getTotalRetraitsAttribute()
    {
        return $this->montant_retrait;
    }

    // Méthode pour vérifier si un retrait est possible
    public function peutRetirer($montant)
    {
        return $this->balance_caisse >= $montant;
    }

    // Méthode pour vérifier si un remboursement est possible
    public function peutRembourser($montant)
    {
        return $this->getMontantEmprunteAttribute() >= $montant;
    }
}
