<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function edit(User $user): Response
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'email' => $user->email,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'max:50'],
            'email'      => ['required', 'max:50', 'email', Rule::unique('users')->ignore($request->id)],
            'password'   => ['nullable'],
        ]);

        if ($request->password && $request->password !== $request->password_confirmation) {
            $message = new MessageBag();
            $message->add('error_controller', 'Password no coinciden.');

            return back()->withErrors($message);
        }

        $user = User::find($request->id);

        if (!$user) {
            $message = new MessageBag();
            $message->add('error_controller', 'Existió un problema al actualizar.');

            return back()->withErrors($message);
        }

        DB::beginTransaction();

        $user->first_name  = $request->first_name;
        $user->email       = $request->email;

        if ($request->password) {
            $user->password = $request->password;
        }

        $user->save();

        DB::commit();

        return Redirect::back()->with('success', 'Usuario Actualizado');
    }
}
