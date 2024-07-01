<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Section::firstOrCreate([
            'name'  => 'Basico',
            'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
        ]);

        Section::firstOrCreate([
            'name'  => 'Tarjeta Crédito',
            'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
        ]);

        Section::firstOrCreate([
            'name'  => 'Amigo Deuda',
            'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6),
        ]);
    }
}



