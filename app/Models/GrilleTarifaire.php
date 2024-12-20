<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrilleTarifaire extends Model
{
    protected $table = 'grille_tarifaires';
    protected $primaryKey = 'id_grille_tarifaire';

    protected $fillable = [
        'type_transaction_id',
        'produit_id',
        'montant_min',
        'montant_max',
        'commission_grille_tarifaire'
    ];

    protected $casts = [
        'montant_min' => 'float',
        'montant_max' => 'float',
        'commission_grille_tarifaire' => 'float'
    ];

    public function typeTransaction()
    {
        return $this->belongsTo(TypeTransaction::class, 'type_transaction_id', 'id_type_transa');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id', 'id_prod');
    }
}
