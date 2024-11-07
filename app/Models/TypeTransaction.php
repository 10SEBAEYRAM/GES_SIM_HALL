<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeTransaction extends Model
{
    use HasFactory;

    // Définition de la clé primaire si elle n'est pas 'id'
    protected $primaryKey = 'id_type_transa';

    // Définition des colonnes pouvant être remplies en masse
    protected $fillable = ['nom_type_transa'];

    // Relation avec les transactions
    public function transactions(): HasMany
    {
        // La relation inverse de 'belongsTo' dans le modèle 'Transaction'
        return $this->hasMany(Transaction::class, 'type_transaction_id', 'id_type_transa');
    }
}
