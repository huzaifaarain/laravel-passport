<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/oauth/before-authorize', function () {
    $user = request()->user();
    $tokens = $user->tokens()
        ->where('client_id', request()->client_id)
        ->whereRaw(DB::raw('expires_at > updated_at'))
        ->where('revoked', 0);
    if ($tokens->exists() && $tokens->count() == 1) {
        redirect('');
    }
})->middleware('auth');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/developers', App\Http\Controllers\DeveloperController::class)
    ->middleware(['auth'])
    ->name('developers');
