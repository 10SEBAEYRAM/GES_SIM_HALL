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
            TypeTransactionSeeder::class,
            ProduitSeeder::class,
        ]);

        User::factory()->create([
            'nom_util' => 'Test',
            'prenom_util' => 'Test',
            'email_util' => 'test@example.com',
            'num_util' => '0000000000',
            'adress_util' => 'Test',
            'type_users_id' => 1,    
            'password' => Hash::make('password'),
        ]);
    }
}
