<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'produits';  // Assurez-vous que c'est le bon nom de table

    protected $primaryKey = 'id_prod';
    
    protected $fillable = [
        'nom_prod',
        'balance',
        'actif'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'actif' => 'boolean'
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
            ->value('commission_grille_tarifaire') ?? 0;
    }
    public function updateBalance()
    {
        $this->balance = $this->transactions->sum('montant_trans'); // Par exemple, la somme de toutes les transactions liées au produit
        $this->save();
    }
   
}