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
        'commission_grille_tarifaire',
        'frais_service',
        'num_beneficiaire',
        'statut',
        'solde_avant',
        'solde_apres',
        'solde_caisse_avant',
        'solde_caisse_apres',
        'motif',
        'id_caisse'
    ];

    protected $casts = [
        'montant_trans' => 'decimal:2',
        'commission_grille_tarifaire' => 'decimal:2',
        'frais_service' => 'decimal:2',
        'solde_avant' => 'decimal:2',
        'solde_apres' => 'decimal:2',
        'solde_caisse_avant' => 'decimal:2',
        'solde_caisse_apres' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function calculateCommission()
    {
        // Récupérer la grille tarifaire correspondante
        $grilleTarifaire = GrilleTarifaire::where('type_transaction_id', $this->type_transaction_id)
            ->where('produit_id', $this->produit_id)
            ->where('montant_min', '<=', $this->montant_trans)
            ->where('montant_max', '>=', $this->montant_trans)
            ->first();

        if (!$grilleTarifaire) {
            // Si aucune grille n'est trouvée, retourner 0 ou lancer une exception selon vos besoins
            throw new \Exception("Aucune grille tarifaire trouvée pour cette transaction");
        }

        return $grilleTarifaire->commission_grille_tarifaire;
    }

    // Relations
    public function typeTransaction()
    {
        return $this->belongsTo(TypeTransaction::class, 'type_transaction_id', 'id_type_transa');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id', 'id_prod');
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_util'
        );
    }

    // Méthode pour vérifier si la transaction est un dépôt
    public function isDépôt()
    {
        return $this->typeTransaction && $this->typeTransaction->nom_type_transa === 'Dépôt';
    }

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

    // Getter et setter pour le motif
    public function setMotifAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['motif'] = implode(',', $value);
        } else {
            $this->attributes['motif'] = (string) $value;
        }
    }

    public function getFormattedMotifAttribute()
    {
        if (empty($this->motif)) {
            return 'Aucun motif spécifié';
        }

        $motif = $this->attributes['motif'];
        return strpos($motif, ',') !== false ? explode(',', $motif) : $motif;
    }

    public function getMotifAttribute($value)
    {
        return empty($value) ? '' : $value;
    }

    // Format le montant en FCFA
    public function formatAmount($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    // Calcul du solde après pour un produit
    public function getSoldeApresCalcule()
    {
        $type = $this->typeTransaction->nom_type_transa ?? '';

        if ($type === 'Dépôt') {
            return $this->solde_avant + $this->montant_trans + $this->commission_grille_tarifaire;
        } elseif ($type === 'Retrait') {
            return $this->solde_avant - $this->montant_trans + $this->commission_grille_tarifaire;
        }
        return $this->solde_avant + $this->commission_grille_tarifaire;
    }

    // Calcul du solde après pour la caisse
    public function getSoldeCaisseApresCalcule()
    {
        $caisse = Caisse::where('id_caisse', $this->id_caisse)->first();

        if (!$caisse) {
            return 0;
        }

        $solde_caisse_avant = $caisse->balance_caisse;

        $type = $this->typeTransaction->nom_type_transa ?? '';

        if ($type === 'Dépôt') {
            return $solde_caisse_avant + $this->montant_trans + $this->frais_service;
        } elseif ($type === 'Retrait') {
            return $solde_caisse_avant - $this->montant_trans - $this->frais_service;
        }
        return $solde_caisse_avant;
    }

    // Mise à jour du solde produit après transaction
    public function updateSoldeProduit()
    {
        $produit = $this->produit;
        if ($produit) {
            $produit->balance = $this->getSoldeApresCalcule();
            $produit->save();
        }
    }

    // Mise à jour du solde de la caisse après transaction
    public function updateSoldeCaisse()
    {
        $caisse = Caisse::where('id_caisse', $this->id_caisse)->first();

        if ($caisse) {
            $caisse->balance_caisse = $this->getSoldeCaisseApresCalcule();
            $caisse->save();
        }
    }

    // Vérifie si la transaction est terminée
    public function isComplete()
    {
        return $this->statut === self::STATUT_COMPLETE;
    }

    // Vérifie si la transaction est annulée
    public function isCancelled()
    {
        return $this->statut === self::STATUT_ANNULE;
    }
}
