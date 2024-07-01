<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');


// Users

Route::get('users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/update', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

// Reports

Route::get('reports/{year}/{month}', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

Route::post('reports/store', [ReportsController::class, 'store'])
    ->name('reports.store')
    ->middleware('auth');

Route::post('reports/update', [ReportsController::class, 'update'])
    ->name('reports.update')
    ->middleware('auth');

Route::post('reports/pay', [ReportsController::class, 'pay'])
    ->name('reports.pay')
    ->middleware('auth');

Route::post('reports/remove', [ReportsController::class, 'remove'])
    ->name('reports.remove')
    ->middleware('auth');

Route::post('reports/duplicate', [ReportsController::class, 'duplicate'])
    ->name('reports.duplicate')
    ->middleware('auth');

Route::get('/', [ReportsController::class, 'redirect'])
    ->name('redirect');

