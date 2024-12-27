<?php

namespace Database\Factories;

use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    public function definition()
    {
        return [
            'nom_prod' => $this->faker->unique()->word,
            'balance' => $this->faker->randomFloat(2, 0, 1000000),
            'status' => $this->faker->boolean,
        ];
    }
}
