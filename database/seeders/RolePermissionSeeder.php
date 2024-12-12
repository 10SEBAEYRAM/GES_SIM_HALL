<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Types d'utilisateurs et leurs permissions correspondantes
        $userTypes = [
            'Administrateur' => [
                'create-users',
                'edit-users',
                'delete-users',
                'view-users',
                'create-products',
                'edit-products',
                'delete-products',
                'view-products',
                'create-transactions',
                'edit-transactions',
                'view-transactions',
                'delete-transactions',
                'create-caisses',
                'edit-caisses',
                'delete-caisses',
                'create-grille_tarifaires',
                'edit-grille_tarifaires',
                'delete-grille_tarifaires',
                'manage-tariff-grids'
            ],
            'Caissier' => [
                'view-transactions',
                'create-transactions',
                'edit-transactions',
                'manage-caisses'
            ],
            'Opérateur' => [
                'create-transactions',
                'edit-transactions',
                'view-transactions'
            ]
        ];

        // Parcourir chaque type d'utilisateur
        foreach ($userTypes as $typeName => $permissions) {
            // Trouver ou créer le type d'utilisateur
            $typeUser = TypeUser::firstOrCreate(['nom_type_users' => $typeName]);

            // Créer ou trouver le rôle correspondant
            $role = Role::firstOrCreate(['name' => $typeName]);

            // Créer et attacher les permissions
            foreach ($permissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            }

            // Attacher le rôle au type d'utilisateur
            $typeUser->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
