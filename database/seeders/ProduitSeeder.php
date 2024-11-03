<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produit;

class ProduitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $produits = [
            ['nom_prod' => 'FLOOZ', 'balance' => 0, 'actif' => true],
            ['nom_prod' => 'TMONEY', 'balance' => 0, 'actif' => true],
            ['nom_prod' => 'WESTEN UNION', 'balance' => 0, 'actif' => true],
        ];

        foreach ($produits as $produit) {
            Produit::create($produit);
        }
    }
}
