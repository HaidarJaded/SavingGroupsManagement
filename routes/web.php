<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SavingGroupController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TransactionController;

Route::get('login', [AuthenticatedSessionController::class, 'showLoginPage'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'login']);
Route::get('/subscriber/login', [SubscriberController::class, 'showLoginForm'])->name('subscriber.login');
Route::post('/subscriber/login', [SubscriberController::class, 'login'])->name('subscriber.login.submit');
Route::get('/subscriber/payments', [SubscriberController::class, 'showPaymentsByCode'])->name('subscriber.payments');

Route::middleware('auth')->group(function () {
    Route::get('saving_group/payments/{saving_group_id}', [SavingGroupController::class, 'getSavingGroupPayments'])->name('saving_group_payments');
    Route::resource('saving_groups',SavingGroupController::class);
    Route::get('transactions/{saving_group_id}',[TransactionController::class,'create'])->name('transactions.create');
    Route::get('transactions',[TransactionController::class,'index'])->name('transactions.index');
    Route::post('transactions',[TransactionController::class,'store'])->name('transactions.store');
    Route::delete('transactions/destroy/{transaction_id}',[TransactionController::class,'destroy'])->name('transactions.destroy');
    Route::resource('payments',PaymentController::class);
    Route::get('saving_group_subscribers/{id}',[SavingGroupController::class,'saving_group_subscribers'])->name('saving_group_subscribers');
    Route::get('payments/subscriber/{id}',[SubscriberController::class,'show_payments'])->name('payments.subscriber');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});