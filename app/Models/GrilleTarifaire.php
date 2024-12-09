<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrilleTarifaire extends Model
{

    protected $primaryKey = 'id_grille_tarifaire';
    
    protected $fillable = [
        'produit_id',
        'montant_min',
        'montant_max',
        'commission_grille_tarifaire',
    ];

    protected $casts = [
        'date_validite' => 'date'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}
