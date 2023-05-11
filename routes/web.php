<?php

use App\Http\Controllers\FindAllUsers;
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

Route::middleware("auth")->get("/users",[FindAllUsers::class,'index']);
Route::middleware('auth')->get("/send",function (){
    return  \App\Models\User::all();
});
Route::group(['prefix'=>'api/chat','as'=>'chat.'], function (){
    Route::get("/user/{user:id}",function (\App\Models\User $user){
      return  $user;
    });

    Route::get('/allMessages/{user:id}',[MessageController::class,'allMessages'])->name("allMessages");
    Route::post('/storeMessage/{user:id}',[MessageController::class,'store'])->name("store");

})->middleware('auth');


