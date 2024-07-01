<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\ItemUser;
use App\Models\UserSection;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sections = DB::table('sections')->get()->keyBy('id');
        $users    = DB::table('sections')->get()->keyBy('id');

        $item = Item::firstOrCreate([
            'section_id' => $sections[1]->id,
            'month'      => Carbon::now()->format('Y-m'),
            'name'       => 'Arriendo',
            'amount'     => 450000,
            'icon'       => 'fa-solid fa-house',
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[1]->id,
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[2]->id,
        ]);

        $item = Item::firstOrCreate([
            'section_id' => $sections[1]->id,
            'month'      => Carbon::now()->format('Y-m'),
            'name'       => 'Luz',
            'amount'     => 35000,
            'icon'       => 'fa-solid fa-bolt',
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[1]->id,
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[2]->id,
        ]);

        $item = Item::firstOrCreate([
            'section_id' => $sections[1]->id,
            'month'      => Carbon::now()->format('Y-m'),
            'name'       => 'Netflix',
            'amount'     => 16500,
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[1]->id,
        ]);

        $item = Item::firstOrCreate([
            'section_id' => $sections[1]->id,
            'month'      => Carbon::now()->format('Y-m'),
            'name'       => 'HBO',
            'amount'     => 5500,
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[2]->id,
        ]);

        $item = Item::firstOrCreate([
            'section_id' => $sections[2]->id,
            'month'      => Carbon::now()->format('Y-m'),
            'name'       => 'Avance Crédito',
            'amount'     => 210000,
            'quota'      => 1,
            'quota_total'=> 48
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[1]->id,
        ]);

        $item = Item::firstOrCreate([
            'section_id' => $sections[3]->id,
            'month'      => Carbon::now()->format('Y-m'),
            'name'       => 'Prestamo',
            'amount'     => 30000,
            'quota'      => 1,
            'quota_total'=> 3
        ]);

        ItemUser::firstOrCreate([
            'item_id' => $item->id,
            'user_id' => $users[3]->id,
        ]);

        // SECTIONS
        UserSection::firstOrCreate([
            'user_id'    => $users[3]->id,
            'section_id' => $sections[3]->id,
        ]);
    }
}



