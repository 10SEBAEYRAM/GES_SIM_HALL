<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeUser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {

        Role::query()->delete();
        Permission::query()->delete();

        $userTypes = [
            'Administrateur' => [
                'create-users',
                'edit-users',
                'delete-users',
                'view-users',
                'create-produits',
                'edit-produits',
                'delete-produits',
                'view-produits',
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
                'manage-tariff-grids',
                'create-type_transactions',
                'delete-type_transactions',
                'edit-type_transactions',

            ],
            'Caissier' => [
                'view-transactions',
                'create-transactions',
                'edit-transactions',

            ],
            // 'Opérateur' => [
            //     'create-transactions',
            //     'edit-transactions',
            //     'view-transactions'
            // ]
        ];

        foreach ($userTypes as $typeName => $permissions) {

            $role = Role::firstOrCreate(['name' => $typeName]);

            $rolePermissions = [];

            foreach ($permissions as $permissionName) {
                // Créer ou récupérer la permission
                $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
                $rolePermissions[] = $permission->id;
            }

            // Attacher les permissions au rôle sans duplication
            $role->permissions()->sync($rolePermissions);
        }
    }
}
