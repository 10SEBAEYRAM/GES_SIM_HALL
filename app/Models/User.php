<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\TypeUser;
use App\Models\Transaction;

class User extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $primaryKey = 'id_util';

    /**
     * Définition de la table associée à ce modèle.
     *
     * @var string
     */
    protected $table = 'users'; 

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_util',
        'prenom_util',
        'email_util',
        'num_util',
        'adress_util',
        'password',
        'type_users_id'
    ];

    /**
     * Les attributs qui doivent être cachés pour les tableaux.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relation avec le modèle TypeUser.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function typeUser()
    {
        return $this->belongsTo(TypeUser::class, 'type_users_id');
    }

    /**
     * Relation avec le modèle Transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Attribut personnalisé pour obtenir le nom complet.
     *
     * @return string
     */
    public function getNomCompletAttribute()
    {
        return "{$this->nom_util} {$this->prenom_util}";
    }

    /**
     * Les attributs qui doivent être castés.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Retourne le nom de l'identifiant utilisé pour l'authentification.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email_util';
    }
}
