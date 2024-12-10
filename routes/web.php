<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use Illuminate\Http\Request;
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

 Route::get('/', function () {
  return view('welcome');
 });

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' =>'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    
    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);
    
    Route::resource('restaurants', Admin\RestaurantController::class);

    Route::resource('categories', Admin\CategoryController::class);
   
    Route::resource('company', Admin\CompanyController::class);
   
    Route::resource('terms', Admin\TermController::class);
});

Route::group(['middleware' => 'guest:admin'], function () {
     Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

     Route::resource('restaurants', RestaurantController::class)->only(['index', 'show']);

  Route::group(['middleware' => ['auth', 'verified']], function() {
     Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
  });
});

Route::group(['middleware'=> ['guest:admin', 'auth', 'verified', NotSubscribed::class]], function() {
   Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
   Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
});

Route::group(['middleware'=> ['guest:admin', 'auth', 'verified', Subscribed::class]], function() {
   Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
   Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
   Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
   Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
});