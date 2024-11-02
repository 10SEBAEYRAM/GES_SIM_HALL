<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_prod';
    
    protected $fillable = [
        'nom_prod',
        'date_prod',
        'balance',
        'actif'
    ];

    protected $casts = [
        'date_prod' => 'date',
        'actif' => 'boolean',
    ];

    public function grilleTarifaires()
    {
        return $this->hasMany(GrilleTarifaire::class, 'produit_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'produit_id');
    }

    public function getCommissionForAmount($montant)
    {
        return $this->grilleTarifaires()
            ->where('montant_min', '<=', $montant)
            ->where('montant_max', '>=', $montant)
            ->where('date_validite', '>=', now())
            ->first()
            ?->commission_grille_tarifaire ?? 0;
    }

    public function updateBalance($montant, $type_operation)
    {
        $this->balance = $type_operation === 'DEPOT' 
            ? $this->balance + $montant 
            : $this->balance - $montant;
        $this->save();
    }
}