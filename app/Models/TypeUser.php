<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeUser extends Model
{
    use HasRoles;

    protected $primaryKey = 'id_type_users';
    protected $fillable = ['nom_type_users'];

    public function users()
    {
        return $this->hasMany(User::class, 'type_users_id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id');
    }
}
