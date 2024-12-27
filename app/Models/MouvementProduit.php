<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MouvementProduit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mouvements_produits';
    protected $primaryKey = 'id_mouvement';

    protected $fillable = [
        'produit_id',
        'type_mouvement',
        'description',
        'volume_depot',
        'valeur_depot',
        'commission_depot',
        'volume_retrait',
        'valeur_retrait',
        'commission_retrait',
        'montant_ht',
        'retenue',
        'montant_net',
        'commission_produit'  // Ajout du nouveau champ

    ];

    protected $casts = [
        'valeur_depot' => 'decimal:2',
        'commission_depot' => 'decimal:2',
        'valeur_retrait' => 'decimal:2',
        'commission_retrait' => 'decimal:2',
        'montant_ht' => 'decimal:2',
        'retenue' => 'decimal:2',
        'montant_net' => 'decimal:2',
        'commission_produit' => 'decimal:2'  // Cast pour le nouveau champ

    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id', 'id_prod');
    }

     // Méthode pour calculer la commission totale du produit
     public function getCommissionProduitAttribute()
     {
         return $this->commission_depot + $this->commission_retrait;
     }
}
