<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Définition de la table associée à ce modèle.
     *
     * @var string
     */
    protected $table = 'users'; 

    /**

     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_utili',
        'prenom_utili',
        'email_utili',
        'num_utili',
        'password',
    ];

    /**
   
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  
    ];

    /**
    
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'email_utili';
    }
}
