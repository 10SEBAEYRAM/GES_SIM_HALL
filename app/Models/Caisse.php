<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    use HasFactory;

    // Si votre clé primaire n'est pas 'id'
    protected $primaryKey = 'id_caisse';

    // Si la clé primaire n'est pas auto-incrémentée, ajoutez ceci (si nécessaire)
    public $incrementing = false;

    // Si votre clé primaire n'est pas de type entier, par exemple si c'est une chaîne
    protected $keyType = 'string';

    protected $fillable = [
        'balance_caisse',
        'nom_caisse',
    ];
}
