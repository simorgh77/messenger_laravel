<?php

use App\Http\Controllers\MessageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use const App\Http\Controllers\FindAllUsers;

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
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware("auth")->get("/users",function (){
    return  \App\Models\User::all();
});
Route::middleware('auth')->get("/send",function (){
    return  \App\Models\User::all();
});
Route::group(['prefix'=>'api/chat','as'=>'chat.'], function (){

    Route::get('/receivingMessages/{user:id}',[MessageController::class,'receivingMessages'])->name("receivingMessages");
    Route::get('/sendingMassages/{user:id}',[MessageController::class,'sendingMassages'])->name("sendingMassages");
    Route::post('/storeMessage/{user:id}',[MessageController::class,'store'])->name("store")->middleware('auth:sanctum');

})->middleware('auth');
