<?php

use App\Http\Controllers\TypingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [TypingController::class, 'show'])->name('dashboard');

    Route::get('/sentence', [TypingController::class, 'showSentence'])->name('sentence');
    Route::get('/sentences', [TypingController::class, 'getSentences'])->name('sentences');
    Route::put('/sentence/store', [TypingController::class, 'storeSentence'])->name('sentence.store');
    Route::put('/sentence/update', [TypingController::class, 'updateSentence'])->name('sentence.update');
    Route::delete('/sentence/delete', [TypingController::class, 'deleteSentence'])->name('sentence.delete');

    Route::get('/preference', [TypingController::class, 'showPreference'])->name('preference');
    Route::put('/preference/store', [TypingController::class, 'storePreference'])->name('preference.store');
});
