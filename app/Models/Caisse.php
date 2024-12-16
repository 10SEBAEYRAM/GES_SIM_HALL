<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    use HasFactory;

    // Si votre clé primaire n'est pas 'id'
    protected $table = 'caisses'; // Associe ce modèle à la table "caisses"
    protected $primaryKey = 'id_caisse'; // Spécifie que la clé primaire est "id_caisse"
    public $incrementing = true; // Indique que la clé primaire est auto-incrémentée
    protected $keyType = 'int'; // Type de la clé primaire

    protected $fillable = [
        'balance_caisse',
        'nom_caisse',
        'emprunt_sim_hall',
        'montant_retrait',
        'remboursement_sim_hall',
    ];
}
