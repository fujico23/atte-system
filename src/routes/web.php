<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Middleware\AuthMiddleware;
use Laravel\SerializableClosure\Serializers\Signed;
use Illuminate\Http\Request;

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

Route::middleware('auth','verified')->group(function () {
    Route::get('/', [AuthController::class, 'create']);
    Route::post('/store', [AuthController::class, 'store']);
    Route::get('/attendance/{date?}', [AuthController::class, 'index'])->name('attendance.index');
    Route::get('/list', [AuthController::class, 'list']);
    Route::get('/detail/{id}/{month?}', [AuthController::class, 'show'])->name('detail.show');
    Route::post('/export/{id}/{month?}', [AuthController::class, 'export'])->name('export.csv');
});

//メール確認の通知
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//メール確認のリンクをクリックした後の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

//メール確認の再送信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.resend');
