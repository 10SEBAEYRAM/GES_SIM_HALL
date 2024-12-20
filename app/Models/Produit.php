<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use SoftDeletes;

    protected $table = 'produits';
    protected $primaryKey = 'id_prod';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nom_prod',
        'balance',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean',
        'balance' => 'float'
    ];

    // Validation statique
    public static function rules($id = null)
    {
        $uniqueRule = $id
            ? 'unique:produits,nom_prod,' . $id . ',id_prod'
            : 'unique:produits,nom_prod';

        return [
            'nom_prod' => ['required', 'string', 'max:50', $uniqueRule],
            'balance' => 'required|numeric|min:0',
            'actif' => 'nullable|boolean',
        ];
    }

    // Messages d'erreur personnalisés
    public static function validationMessages()
    {
        return [
            'nom_prod.unique' => 'Ce nom de produit existe déjà.',
            'balance.numeric' => 'La balance doit être un nombre.',
            'balance.min' => 'La balance ne peut pas être négative.',
        ];
    }

    public function grilleTarifaires()
    {
        return $this->hasMany(GrilleTarifaire::class, 'produit_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'produit_id');
    }

    public function getCommissionForAmount($montant, $type_transaction_id)
    {
        // Validate input parameters
        if (!is_numeric($montant) || $montant < 0) {
            throw new \Exception("Le montant doit être un nombre positif");
        }

        // Get all matching grids for debugging
        $matchingGrids = $this->grilleTarifaires()
            ->where('type_transaction_id', $type_transaction_id)
            ->get();

        if ($matchingGrids->isEmpty()) {
            throw new \Exception("Aucune grille tarifaire trouvée pour ce type de transaction");
        }

        // Find the specific grid for the amount
        $commission = $this->grilleTarifaires()
            ->where('type_transaction_id', $type_transaction_id)
            ->where('montant_min', '<=', $montant)
            ->where('montant_max', '>=', $montant)
            ->value('commission_grille_tarifaire');
            
        if ($commission === null) {
            // Get the available ranges for better error message
            $ranges = $matchingGrids->map(function($grid) {
                return "({$grid->montant_min} - {$grid->montant_max})";
            })->join(', ');
            
            throw new \Exception(
                "Aucune commission trouvée pour le montant {$montant}. " .
                "Plages disponibles: {$ranges}"
            );
        }
        
        return $commission;
    }

    public function updateBalance()
    {
        $this->balance = $this->transactions->sum('montant_trans');
        $this->save();
    }
}
