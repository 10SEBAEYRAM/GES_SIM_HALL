<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // Relation avec les permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    // Relation avec les types d'utilisateurs
    public function typeUsers()
    {
        return $this->belongsToMany(TypeUser::class, 'type_user_has_roles');
    }
}
