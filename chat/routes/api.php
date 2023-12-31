<?php

use App\Http\Controllers\Chat\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\ProfileUserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function () {
    Route::post('/registro', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refrescar', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/perfil', [AuthController::class, 'profile'])->name('profile');
});

Route::group(['prefix' => 'user'], function($router){
    Route::post('/profile-user', [ProfileUserController::class, 'profileUser']);
    Route::get('/contact-users', [ProfileUserController::class, 'contactUsers']);
});

Route::group(['prefix'=> 'chat'], function($router){
    Route::post('/start-chat', [ChatController::class,'startChat']);
    Route::post('/list-my-chat-room', [ChatController::class,'listMyChats']);
    Route::post('/send-message-text', [ChatController::class,'sendMessageText']);
    Route::post('/send-message-text-and-files', [ChatController::class,'sendFileMessageText']);
});
