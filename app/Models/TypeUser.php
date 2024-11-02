<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeUser extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id_type_users';
    protected $fillable = ['nom_type_users'];

    public function users()
    {
        return $this->hasMany(User::class, 'type_users_id');
    }
}