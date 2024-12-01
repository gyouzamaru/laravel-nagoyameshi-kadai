<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\Category;

class HomeController extends Controller
{
   public function index() {
    $highly_rated_restaurants = Restaurant::take(6)->get();

    $categories = Category::all();

    $new_restaurants = Restaurant::take(6)->orderBy('created_at','desc')->get();

    return view('home', compact('highly_rated_restaurants', 'categories', 'new_restaurants'));
}

}
