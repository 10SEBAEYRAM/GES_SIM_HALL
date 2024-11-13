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

    protected $primaryKey = 'id_util';  // Définir la clé primaire si ce n'est pas 'id'
    protected $table = 'users';         // Table associée

    protected $fillable = [
        'nom_util',
        'prenom_util',
        'email_util',
        'num_util',
        'adress_util',
        'password',
        'type_users_id',   // Clé étrangère vers TypeUser
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relation avec le modèle TypeUser.
     */
    public function typeUser()
    {
        return $this->belongsTo(TypeUser::class, 'type_users_id', 'id_type_users');  // Clé étrangère et clé primaire explicites
    }

    /**
     * Relation avec le modèle Transaction.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id_util');  // Clé étrangère et clé primaire explicitement définies
    }

    /**
     * Attribut personnalisé pour obtenir le nom complet de l'utilisateur.
     */
    public function getNomCompletAttribute()
    {
        return "{$this->nom_util} {$this->prenom_util}";  // Retourne le nom complet
    }

    /**
     * Retourne le nom de l'identifiant utilisé pour l'authentification.
     */
    public function getAuthIdentifierName()
    {
        return 'email_util';  // Utilise l'email pour l'authentification
    }

    /**
     * Retourne le mot de passe pour l'utilisateur.
     */
    public function getAuthPassword()
    {
        return $this->password;  // Retourne le mot de passe haché de l'utilisateur
    }

    /**
     * Retourne l'identifiant pour l'utilisateur.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};  // Utilise l'email comme identifiant pour l'utilisateur
    }
}
