<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\MessageBag;
use Carbon\Carbon;

use App\Models\Item;
use App\Models\ItemUser;

class ReportsController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            $year  = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            
            return Redirect::route('reports', ['year' => $year, 'month' => $month]);
        }
        else {
            return Redirect::route('login');
        }
    }

    public function index(Request $request): Response
    {
        $month = $request->year.'-'.$request->month;
        $userId = Auth::user()->id;

        $items = DB::table('items as i')
                    ->join('sections as s', 'i.section_id', 's.id')
                    ->leftJoin('users_sections as us', function ($join) use ($userId) {
                        $join->on('s.id', '!=', 'us.section_id')
                             ->where('us.user_id', '=', $userId);
                    })
                    ->select(
                        'i.id as item_id',
                        'i.name as item_name',
                        'amount',
                        's.name as section_name',
                        'i.icon',
                        DB::raw("CONCAT(i.quota, '/', i.quota_total) as quota"),
                        'i.payed'
                    )
                    ->where('month', $month)
                    ->where('us.section_id')
                    ->whereNull('i.deleted_at')
                    ->get();
            
        $itemsUsers = DB::table('items_users as iu')
                        ->join('users as u', 'iu.user_id', 'u.id')
                        ->select(
                            'u.id',
                            'u.first_name as name',
                            'iu.item_id'
                        )
                        ->whereIn('item_id', $items->pluck('item_id'))
                        ->get()
                        ->groupBy('item_id')->toArray();


        $items->each(function ($item) use ($itemsUsers) {
            $item->users = $itemsUsers[$item->item_id];
            $item->split = $item->amount / count($itemsUsers[$item->item_id]);
        }); 

        $items = $items->groupBy('section_name');
        
        $usersSections = DB::table('items_users as iu')
                            ->join('items as i', 'iu.item_id', 'i.id')
                            ->join('sections as s', 'i.section_id', 's.id')
                            ->join('users as u', 'iu.user_id', 'u.id')
                            ->whereNull('i.deleted_at')
                            ->select(
                                's.name as section_name',
                                'iu.user_id',
                                'u.first_name as name',
                            )
                            ->get();

        $usersSections = $usersSections->groupBy('section_name')->map(function ($group) {
            return $group->keyBy('user_id')->toArray();
        });

        $sectionColor = DB::table('sections')
                            ->pluck('color', 'name');

        $sectionsSelect = DB::table('sections')
                            ->get();

        $userSelect = DB::table('users')
                        ->select(
                            'id',
                            'first_name as name',
                        )
                        ->get();

        return Inertia::render('Reports/Index', compact(
            'items', 
            'usersSections', 
            'sectionColor', 
            'month',
            'sectionsSelect',
            'userSelect'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'month'   => ['required'],
            'section' => ['required'],
            'item'    => ['required'],
            'amount'  => ['required'],
            'users'   => ['required', 'array', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            if($request->quota) {
                $quota = explode('/', $request->quota);
            }

            $new = Item::create([
                'month'       => Carbon::parse($request->month)->format('Y-m'),
                'section_id'  => $request->section['id'],
                'name'        => $request->item,
                'amount'      => $request->quota ? (int)($request->amount / (int)$quota[1]) : $request->amount,
                'quota'       => $request->quota ? (int)$quota[0] : null,
                'quota_total' => $request->quota ? (int)$quota[1] : null,
                'icon'        => $request->icon,
            ]);

            foreach ($request->users as $user) {
                ItemUser::create([
                    'item_id' => $new->id,
                    'user_id' => $user['id'],
                ]);
            }

            DB::commit();

            $year  = Carbon::parse($request->month)->format('Y');
            $month = Carbon::parse($request->month)->format('m');

            return Redirect::route('reports', ['year' => $year, 'month' => $month])->with('success', "$new->name creado.");

        } catch (ValidationException $exception) {
            $message = new MessageBag();
            $message->add('error_controller', 'Existió un problema al guardar.');

            return back()->withErrors($message); 
        }
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'month'   => ['required'],
            'section' => ['required'],
            'item'    => ['required'],
            'amount'  => ['required'],
            'users'   => ['required', 'array', 'min:1'],
        ]);

        try {

            $item = Item::find($request->id);

            if (!$item) {
                $message = new MessageBag();
                $message->add('error_controller', 'Existió un problema al guardar.');

                return back()->withErrors($message);
            }

            DB::beginTransaction();

            if($request->quota) {
                $quota = explode('/', $request->quota);
            }

            $item->section_id  = $request->section['id'];
            $item->name        = $request->item;
            $item->amount      = $request->amount;
            $item->quota       = $request->quota ? (int)$quota[0] : null;
            $item->quota_total = $request->quota ? (int)$quota[1] : null;
            $item->icon        = $request->icon;
            $item->save();


            $itemsUsers = DB::table('items_users')
                            ->where('item_id', $item->id)
                            ->whereNotIn('user_id', array_column($request->users, 'id'))
                            ->delete();

            $itemIds = array_column($request->users, 'id');
            foreach ($request->users as $user) {
                ItemUser::firstOrCreate([
                    'item_id' => $item->id,
                    'user_id' => $user['id'],
                ]);
            }

            DB::commit();

            $year  = Carbon::parse($request->month)->format('Y');
            $month = Carbon::parse($request->month)->format('m');

            return Redirect::route('reports', ['year' => $year, 'month' => $month])->with('success', "$item->name actualizado.");


        } catch (ValidationException $exception) {
            $message = new MessageBag();
            $message->add('error_controller', 'Existió un problema al guardar.');

            return back()->withErrors($message); 
        }
    }

    public function pay(Request $request): RedirectResponse
    {
        try {
            $item = Item::find($request->id);

            if (!$item) {
                $message = new MessageBag();
                $message->add('error_controller', 'Existió un problema al pagar.');

                return back()->withErrors($message);
            }

            DB::beginTransaction();

            $item->payed = 1;
            $item->payed_date = Carbon::now();
            $item->save();

            DB::commit();

            $year  = Carbon::parse($request->month)->format('Y');
            $month = Carbon::parse($request->month)->format('m');

            return Redirect::route('reports', ['year' => $year, 'month' => $month])->with('success', "$item->name pagado.");
          
        } catch (ValidationException $exception) {
            $message = new MessageBag();
            $message->add('error_controller', 'Existió un problema al pagar.');

            return back()->withErrors($message); 
        }
    }

    public function remove(Request $request): RedirectResponse
    {
        try {
            $item = Item::find($request->id);

            if ($item) {
                $item->delete();

                $year  = Carbon::parse($request->month)->format('Y');
                $month = Carbon::parse($request->month)->format('m');

                return Redirect::route('reports', ['year' => $year, 'month' => $month])->with('success', "$item->name eliminado.");
            }

            return Redirect::route('reports', ['year' => $year, 'month' => $month])->with('success', null);

        } catch (ValidationException $exception) {
            $message = new MessageBag();
            $message->add('error_controller', 'Existió un problema al eliminar.');

            return back()->withErrors($message); 
        }
    }

    public function duplicate(Request $request): RedirectResponse
    {
        try {
            $duplicate  = Carbon::parse($request->month)->format('Y-m');
            $newMonth   = Carbon::parse($request->month)->addMonth()->format('Y-m');

            // CHECK MONTH TO DUPLICATE
            $existItems = DB::table('items')
                            ->where('month', $newMonth)
                            ->whereNull('deleted_at')
                            ->first();

            if ($existItems) {
                $message = new MessageBag();
                $message->add('error_controller', "Ya existen items para el mes $newMonth.");

                return back()->withErrors($message);
            }

            $items = DB::table('items')
                        ->where('month', $duplicate)
                        ->select(
                            'id',
                            'section_id',
                            DB::raw("'{$newMonth}' as month"),
                            'name',
                            'amount',
                            'icon',
                            'quota',
                            'quota_total'
                        )
                        ->whereNull('deleted_at')
                        ->get();
                
            $itemsUsers = DB::table('items_users')
                            ->select(
                                'item_id',
                                'user_id',
                            )
                            ->whereIn('item_id', $items->pluck('id'))
                            ->get()
                            ->groupBy('item_id')->toArray();

            DB::beginTransaction();

            $items->each(function ($item) use ($itemsUsers) {
                $item->users = $itemsUsers[$item->id];

                if (!$item->quota || $item->quota < $item->quota_total) {
                    $new = new Item;
                    $new->section_id  = $item->section_id;
                    $new->month       = $item->month;
                    $new->name        = $item->name;
                    $new->amount      = $item->amount;
                    $new->icon        = $item->icon;
                    $new->quota       = ($item->quota && $item->quota < $item->quota_total) ? $item->quota + 1 : null;
                    $new->quota_total = $item->quota_total;
                    $new->save();
    
                    foreach ($itemsUsers[$item->id] as $user) {
                        $newItemUser = new ItemUser; 
                        $newItemUser->item_id = $new->id;
                        $newItemUser->user_id = $user->user_id;
                        $newItemUser->save();
                    }
                }
            });

            DB::commit();

            $year  = Carbon::parse($request->month)->addMonth()->format('Y');
            $month = Carbon::parse($request->month)->addMonth()->format('m');

            return Redirect::route('reports', ['year' => $year, 'month' => $month])->with('success', "$duplicate duplicado.");

        } catch (ValidationException $exception) {
            $message = new MessageBag();
            $message->add('error_controller', 'Existió un problema al duplicar.');

            return back()->withErrors($message); 
        }
    }
}
