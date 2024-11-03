<?php

namespace Database\Seeders;

use App\Models\TypeTransaction;
use Illuminate\Database\Seeder;

class TypeTransactionSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['nom_type_transa' => 'Dépôt'],
            ['nom_type_transa' => 'Retrait']
        ];

        foreach ($types as $type) {
            TypeTransaction::create($type);
        }
    }
}