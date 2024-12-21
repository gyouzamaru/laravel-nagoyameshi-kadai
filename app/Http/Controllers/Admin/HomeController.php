<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
 public function index() {

   $total_users = User::Count();

   $total_premium_users = DB::table('subscriptions')
                          ->where('stripe_status', 'active')
                          ->count();
   
   $total_free_users = $total_users - $total_premium_users;

   $total_restaurants = Restaurant::Count();

   $total_reservations = Reservation::Count();

   $sales_for_this_month = 300 *	$total_premium_users;

    return view('admin.home', compact('total_users', 'total_premium_users', 'total_free_users', 'total_restaurants', 'total_reservations', 'sales_for_this_month'));
 }
 
}
