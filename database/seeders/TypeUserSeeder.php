<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Seeder;

class TypeUserSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['nom_type_users' => 'Administrateur'],
            ['nom_type_users' => 'Caissier'],
            ['nom_type_users' => 'Opérateur']
        ];

        foreach ($types as $type) {
            TypeUser::create($type);
        }
    }
}
