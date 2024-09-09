<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;


Route::post('password/reset/request',[ResetPasswordController::class,'resetPasswordRequest'])->name('password.email');

Route::post('password/reset/confirm', [ResetPasswordController::class,'resetPasswordConfirm'])->name('password.reset');

Route::get('password/reset/confirm/{token}', [ResetPasswordController::class,'resetPasswordPage']);

Route::get('email/verify/request',[EmailVerificationController::class,'sendEmailVerificationNotification'])->middleware('auth:sanctum');

Route::get('email/verify/confirm/{token}',[EmailVerificationController::class,'emailVerify']);
