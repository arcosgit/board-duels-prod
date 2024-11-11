<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [App\Http\Controllers\StartController::class, 'index'])->name('start');
Route::get('/offline', [App\Http\Controllers\StartController::class, 'offline'])->name('offline');
Route::get('/about', [App\Http\Controllers\StartController::class, 'about'])->name('about');

Route::middleware('have.auth')->group(function () {
    Route::get('/login', [App\Http\Controllers\LoginRegistrationController::class, 'login'])->name('login');
    Route::post('/registration', [App\Http\Controllers\LoginRegistrationController::class, 'registration']);
    Route::post('/login', [App\Http\Controllers\LoginRegistrationController::class, 'authenticate']);
    Route::post('/resetPassword', [App\Http\Controllers\LoginRegistrationController::class, 'resetPassword']);
});



Route::middleware('auth.check')->group(function () {
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile/update/{what}', [App\Http\Controllers\UserController::class, 'update']);
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('/profile/delete', [App\Http\Controllers\UserController::class, 'delete']);
    Route::post('/profile/settings', [App\Http\Controllers\UserController::class, 'updateSettigs']);
    Route::get('/friends', [App\Http\Controllers\FriendController::class, 'index'])->name('user.friend');
    Route::post('/friends/find', [App\Http\Controllers\FriendController::class, 'findUser']);
    Route::post('/friends/invite', [App\Http\Controllers\FriendController::class, 'inviteFriend']);
    Route::post('/friends/add', [App\Http\Controllers\FriendController::class, 'addFriend']);
    Route::post('/friends/delete', [App\Http\Controllers\FriendController::class, 'deleteFriend']);
    Route::post('/friends/refuse', [App\Http\Controllers\FriendController::class, 'refuseFriend']);
    Route::get('/user/show/{id}', [App\Http\Controllers\ShowController::class, 'userShow'])->name('user.show');
    Route::post('/chat/open/{id}', [App\Http\Controllers\ChatController::class, 'open'])->name('chat.open');
    Route::get('/chat/{id}', [App\Http\Controllers\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat', [App\Http\Controllers\ChatController::class, 'store']);
    Route::get('/getmessages', [App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/getuser', [App\Http\Controllers\NotificationController::class, 'getUser']);
    Route::post('/getRequestsFriend', [App\Http\Controllers\NotificationController::class, 'getFriendNotifications']);
    Route::post('/requestToGame', [App\Http\Controllers\GameController::class, 'requestToGame']);
    Route::post('/refuseGame', [App\Http\Controllers\GameController::class, 'refuseGame']);
    Route::post('/acceptGame', [App\Http\Controllers\GameController::class, 'acceptGame']);
    Route::post('/noAcceptGame', [App\Http\Controllers\GameController::class, 'noAcceptGame']);
    Route::get('/game/{game}', [App\Http\Controllers\GameController::class, 'game']);
    Route::post('/game/move', [App\Http\Controllers\GameController::class, 'move']);
    Route::post('/game/timeout', [App\Http\Controllers\GameController::class, 'timeout']);
    Route::post('/game/reset', [App\Http\Controllers\GameController::class, 'reset']);
    Route::post('/game/left', [App\Http\Controllers\GameController::class, 'left']);
    Route::post('/game/end', [App\Http\Controllers\GameController::class, 'end']);
    Route::post('/game/invite', [App\Http\Controllers\GameController::class, 'invite']);
    Route::post('/game/createWithFriend', [App\Http\Controllers\GameController::class, 'createWithFriend']);
    Route::post('/getFriendList', [App\Http\Controllers\BaseController::class, 'friendList']);
    Route::post('/getHistory', [App\Http\Controllers\BaseController::class, 'history']);
});

