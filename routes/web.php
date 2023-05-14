<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TypingController;
use Illuminate\Foundation\Application;
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
    return redirect()->route('login');
})->middleware('guest');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [TypingController::class, 'show'])->name('dashboard');
    Route::post('/result/store', [TypingController::class, 'storeResult'])->name('play.store');

    Route::get('/sentence', [TypingController::class, 'showSentence'])->name('sentence');
    Route::get('/sentences', [TypingController::class, 'getSentences'])->name('sentences');
    Route::put('/sentence/store', [TypingController::class, 'storeSentence'])->name('sentence.store');
    Route::post('/sentences/store', [TypingController::class, 'storeSentences'])->name('sentences.store');
    Route::put('/sentence/update', [TypingController::class, 'updateSentence'])->name('sentence.update');
    Route::delete('/sentence/delete', [TypingController::class, 'deleteSentence'])->name('sentence.delete');

    Route::get('/preference', [TypingController::class, 'showPreference'])->name('preference');
    Route::put('/preference/store', [TypingController::class, 'storePreference'])->name('preference.store');
    Route::put('/preference/store/sentence', [TypingController::class, 'storeSentencePreference'])
        ->name('preference.sentence.store');

    Route::get('/stats', [TypingController::class, 'showStats'])->name('stats');
    Route::delete('/stats/reset/{sentence}', [TypingController::class, 'resetStat'])->name('stats.reset');
    Route::delete('/stats/all/reset', [TypingController::class, 'resetAllStats'])->name('stats.reset.all');

    Route::put('/settings/sound/{setting_play}', [TypingController::class, 'setSound'])
        ->name('settings.sound');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
