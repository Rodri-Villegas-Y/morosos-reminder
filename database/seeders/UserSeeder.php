<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        User::firstOrCreate([
            'first_name' => 'Rodrigo',
            'last_name'  => 'Villegas',
            'email'      => 'rvillegas@r0vy.com',
            'password'   => 'secret',
            'role'       => 'admin',
        ]);

        User::firstOrCreate([
            'first_name'        => 'Pareja',
            'last_name'         => '',
            'email'             => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role'              => 'user',
        ]);

        User::firstOrCreate([
            'first_name'        => 'Amigo',
            'last_name'         => '',
            'email'             => $faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => 'secret',
            'remember_token'    => Str::random(10),
            'role'              => 'user',
        ]);
    }
}



