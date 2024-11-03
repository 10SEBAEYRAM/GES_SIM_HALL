<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeTransaction extends Model
{

    protected $primaryKey = 'id_type_transa';
    protected $fillable = ['nom_type_transa'];

    // Relation avec les transactions
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'type_transaction_id', 'id_type_transa');
    }
}