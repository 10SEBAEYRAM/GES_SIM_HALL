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
        'status',


    ];

    protected $casts = [
        'status' => 'boolean',
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
            'status' => 'nullable|boolean',
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
        // Convert and validate input parameters
        $montant = floatval($montant);
        $type_transaction_id = intval($type_transaction_id);

        if ($montant < 0) {
            throw new \Exception("Le montant doit être un nombre positif");
        }

        // Use explicit type casting in the query
        $commission = $this->grilleTarifaires()
            ->where('type_transaction_id', $type_transaction_id)
            ->whereRaw('CAST(montant_min as DECIMAL(10,2)) <= ?', [$montant])
            ->whereRaw('CAST(montant_max as DECIMAL(10,2)) >= ?', [$montant])
            ->value('commission_grille_tarifaire');

        if ($commission === null) {
            // Get the ranges for error message
            $ranges = $this->grilleTarifaires()
                ->where('type_transaction_id', $type_transaction_id)
                ->get()
                ->map(function ($grid) {
                    return "({$grid->montant_min} - {$grid->montant_max})";
                })->join(', ');

            throw new \Exception(
                "Aucune commission trouvée pour le montant {$montant}. " .
                    "Plages disponibles: {$ranges}"
            );
        }

        return floatval($commission);
    }

    public function updateBalance()
    {
        $this->balance = $this->transactions->sum('montant_trans');
        $this->save();
    }

    public function scopeActif($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactif($query)
    {
        return $query->where('status', false);
    }

   

    public function mouvements()
    {
        return $this->hasMany(MouvementProduit::class, 'produit_id', 'id_prod');
    }
}

    

