<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_breed')->insert([
            ['id' => 1, 'specie_id' => 1, 'name' => 'Poodle'],
            ['id' => 2, 'specie_id' => 1, 'name' => 'Bulldog'],
            ['id' => 3, 'specie_id' => 1, 'name' => 'Labrador'],
            ['id' => 4, 'specie_id' => 2, 'name' => 'Siamês'],
            ['id' => 5, 'specie_id' => 2, 'name' => 'Persa'],
            ['id' => 6, 'specie_id' => 3, 'name' => 'Canário'],
            ['id' => 7, 'specie_id' => 3, 'name' => 'Papagaio'],
            ['id' => 8, 'specie_id' => 4, 'name' => 'Betta'],
            ['id' => 9, 'specie_id' => 4, 'name' => 'Kinguios']
        ]);
    }
}
