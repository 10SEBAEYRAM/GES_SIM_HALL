<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeTransaction extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_type_transa';
    protected $fillable = ['nom_type_transa'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'type_transaction_id');
    }
}
