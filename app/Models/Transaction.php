<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    // Définition des constantes pour les statuts
    const STATUT_EN_COURS = 'EN_COURS';
    const STATUT_COMPLETE = 'COMPLETE';
    const STATUT_ANNULE = 'ANNULE';

    protected $primaryKey = 'id_transaction';
    protected $table = 'transactions';

    protected $fillable = [
        'type_transaction_id',
        'produit_id',
        'user_id',
        'montant_trans',
        'commission_appliquee',
        'frais_service',    
        'num_beneficiaire',
        'statut',
        'solde_avant',
        'solde_apres',
        'solde_caisse_avant',
        'solde_caisse_apres',
        'motif',
        'id_caisse' // Assurez-vous que le champ caisse est inclus si nécessaire
    ];

    protected $casts = [
        'montant_trans' => 'decimal:2',
        'commission_appliquee' => 'decimal:2',
        'solde_avant' => 'decimal:2',
        'solde_apres' => 'decimal:2',
        'solde_caisse_avant' => 'decimal:2',
        'solde_caisse_apres' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function typeTransaction()
    {
        return $this->belongsTo(
            TypeTransaction::class,
            'type_transaction_id',
            'id_type_transa'
        );
    }

    public function produit()
    {
        return $this->belongsTo(
            Produit::class,
            'produit_id',
            'id_prod'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_util'
        );
    }

    // Récupère et calcule les soldes avant et après la transaction
    public static function calculerSoldes($produit_id, $montant, $type_transaction)
    {
        $produit = Produit::where('id_prod', $produit_id)->firstOrFail();
        $solde_avant = $produit->balance;

        if ($type_transaction === 'Dépôt') {
            $solde_apres = $solde_avant + $montant;
        } else {
            $solde_apres = $solde_avant - $montant;
        }

        return [
            'solde_avant' => $solde_avant,
            'solde_apres' => $solde_apres
        ];
    }

    // Gestion du motif
    public function setMotifAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['motif'] = implode(',', $value);
        } else {
            $this->attributes['motif'] = (string) $value;
        }
    }

    // Ajout d'un accesseur formaté pour le motif
    public function getFormattedMotifAttribute()
    {
        if (empty($this->motif)) {
            return 'Aucun motif spécifié';
        }

        $motif = $this->attributes['motif'];
        return strpos($motif, ',') !== false ? explode(',', $motif) : $motif;
    }

    // Accesseur original modifié pour être plus robuste
    public function getMotifAttribute($value)
    {
        if (empty($value)) {
            return '';
        }
        return $value;
    }

    // Méthode utilitaire pour formater les montants
    public function formatAmount($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    // Méthodes utilitaires pour les soldes
    public function getSoldeApresCalcule()
    {
        $type = $this->typeTransaction->nom_type_transa ?? '';
        
        if ($type === 'Dépôt') {
            return $this->solde_avant - $this->montant_trans + $this->commission_appliquee;
        } elseif ($type === 'Retrait') {
            return $this->solde_avant + $this->montant_trans + $this->commission_appliquee;
        }
        return $this->solde_avant + $this->commission_appliquee;
    }

    public function getSoldeCaisseApresCalcule()
    {
        // Récupérer le solde de la caisse depuis la table `caisses`
        $caisse = Caisse::where('id_caisse', $this->id_caisse)->first();

        // Utiliser la balance de la caisse avant la transaction
        $solde_caisse_avant = $caisse ? $caisse->balance_caisse : 0;
        
        $type = $this->typeTransaction->nom_type_transa ?? '';

        if ($type === 'Dépôt') {
            return $solde_caisse_avant + $this->montant_trans;
        } elseif ($type === 'Retrait') {
            return $solde_caisse_avant - $this->montant_trans;
        }
        return $solde_caisse_avant + $this->commission_appliquee;
    }

    // Vérifie si la transaction est complète
    public function isComplete()
    {
        return $this->statut === self::STATUT_COMPLETE;
    }

    // Vérifie si la transaction est annulée
    public function isCancelled()
    {
        return $this->statut === self::STATUT_ANNULE;
    }

    // Met à jour le solde de la caisse après une transaction
    public function updateSoldeCaisse()
    {
        $caisse = Caisse::where('id_caisse', $this->id_caisse)->first();

        if ($caisse) {
            $new_balance = $this->getSoldeCaisseApresCalcule();
            $caisse->update(['balance_caisse' => $new_balance]);
        }
    }
}