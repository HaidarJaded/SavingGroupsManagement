<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SavingGroupController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TransactionController;

Route::get('login', [AuthenticatedSessionController::class, 'showLoginPage'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'login']);
Route::middleware('auth')->group(function () {
    Route::resource('transactions',TransactionController::class);
    Route::resource('saving_groups',SavingGroupController::class);
    Route::get('saving_group_subscribers/{id}',[SavingGroupController::class,'saving_group_subscribers'])->name('saving_group_subscribers');
    Route::get('payments/subscriber/{id}',[SubscriberController::class,'show_payments'])->name('payments.subscriber');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});