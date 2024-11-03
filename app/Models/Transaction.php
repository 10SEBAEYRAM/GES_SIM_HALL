<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id_transaction';

    protected $fillable = [
        'type_transaction_id',
        'produit_id',
        'user_id',
        'montant_trans',
        'commission_appliquee',
        'num_beneficiaire',
        'statut',
        'solde_avant',
        'solde_apres',
        'solde_caisse_avant',
        'solde_caisse_apres'
    ];

    protected $casts = [
        'montant_trans' => 'decimal:2',
        'commission_appliquee' => 'decimal:2',
        'solde_avant' => 'decimal:2',
        'solde_apres' => 'decimal:2',
        'solde_caisse_avant' => 'decimal:2',
        'solde_caisse_apres' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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
        return $this->belongsTo(User::class, 'user_id', 'id_util');
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
}

