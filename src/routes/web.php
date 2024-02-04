<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AuthMiddleware;

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

//ミドルウェアの記述
//Route::middleware('auth')->group(function () {
//    Route::get('/', [AuthController::class, 'create']);
//    Route::get('/', [AuthController::class, 'index']);
//});

Route::get('/', [AuthController::class, 'create']);
Route::post('/store', [AuthController::class, 'store']);
Route::get('/attendance/{date?}', [AuthController::class, 'index'])->name('attendance.index');
Route::get('/attendance/previous', [AuthController::class, 'index'])->name('attendance.previous');
Route::get('/attendance/next', [AuthController::class, 'nextDate'])->name('attendance.next');