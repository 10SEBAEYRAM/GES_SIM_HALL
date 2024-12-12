<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TypeUserSeeder::class,
            RolePermissionSeeder::class,
            TypeTransactionSeeder::class,
            ProduitSeeder::class,
        ]);

        User::factory()->create([
            'nom_util' => 'SEBA',
            'prenom_util' => 'Eyram ',
            'email_util' => 'koffieyramseba@gmail.com',
            'num_util' => '96540056',
            'adress_util' => 'Agbalepedogan',
            'type_users_id' => 1,
            'password' => Hash::make('password'),
        ]);
    }
}
