<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis massivement.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];

    /**
     * Relation avec les types d'utilisateurs via les rôles.
     * Une permission peut être accordée via des rôles ou directement.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function typeUsers()
    {
        return $this->morphedByMany(TypeUser::class, 'model', 'model_has_permissions', 'permission_id', 'model_id');
    }
}
