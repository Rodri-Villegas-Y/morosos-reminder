<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Section;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $account = Account::create(['name' => 'Acme Corporation']);

        $this->call(UserSeeder::class);
        $this->call(SectionSeeder::class);
        $this->call(ItemSeeder::class);

        // User::factory()->create([
        //     'account_id' => $account->id,
        //     'first_name' => 'Rodrigo',
        //     'last_name'  => 'Villegas',
        //     'email'      => 'rvillegas@r0vy.com',
        //     'password'   => 'secret',
        //     'role'       => 'admin',
        // ]);

        // User::factory(2)->create(['account_id' => $account->id]);

        // Section::create([
        //     'name'  => 'Basico',
        //     'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6)
        // ]);

        // Section::create([
        //     'name'  => 'Tarjeta Credito',
        //     'color' => '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6)
        // ]);

        // Contact::factory(100)
        //     ->create(['account_id' => $account->id])
        //     ->each(function ($contact) use ($organizations) {
        //         $contact->update(['organization_id' => $organizations->random()->id]);
        //     });
    }
}
