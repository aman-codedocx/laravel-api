<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/check', function() {
    return "Start API serve on http://127.0.0.1:8000";
});


Route::post('registration', [UserController::class, 'userRegistration'])->name('register.action'); 
Route::post('login', [UserController::class, 'userLogin'])->name('login.action'); 
Route::post('logout', [UserController::class, 'logOut'])->name('logout')->middleware('auth:sanctum');
Route::post('user/invite', [UserController::class, 'userInvitation'])->name('user.invite')->middleware('auth:sanctum');
Route::get('verify', [UserController::class, 'verifyPin'])->name('verify.pin');
Route::post('profile-update', [UserController::class, 'userUpdateProfile'])->name('user.profile.update')->middleware('auth:sanctum');