<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
public function index(Request $request)  {

    $keyword=$request->keyword;

         if($keyword !== null) {
            $restaurants =Restaurant::where('name','like',"%{$keyword}%")
            ->paginate(15);
         } else {
            $restaurants = Restaurant::paginate(15);
         }
         $total = $restaurants->total();
         return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
         }

public function show(Restaurant $restaurant) {
         return view('admin.restaurants.show', compact('restaurant'));
         }


public function create() {
        return view('admin.restaurants.create');
        }

public function store(Request $request) {
    $request->validate([
        'name'=>'required',
        'image'=>'image|max:2048',
        'description'=>'required',
        'lowest_price'=>'required|numeric|min:0|lte:highest_price',
        'highest_price'=>'required|numeric|min:0|gte:lowest_price',
        'postal_code'=>'required|digits:7',
        'address'=>'required',
        'opening_time'=>'required|before:closing_time',
        'closing_time'=>'required|after:opening_time',
        'seating_capacity'=>'required|numeric|min:0',
    ]);

    $restaurant = new Restaurant();
    $restaurant->name = $request->input('name');
    $restaurant->description = $request->input('description');
    $restaurant->lowest_price = $request->input('lowest_price');
    $restaurant->highest_price = $request->input('highest_price');
    $restaurant->postal_code = $request->input('postal_code');
    $restaurant->address = $request->input('address');
    $restaurant->opening_time = $request->input('opening_time');
    $restaurant->closing_time = $request->input('closing_time');
    $restaurant->seating_capacity = $request->input('seating_capacity');

    if ($request->hasFile('image')) {
        $image= $request->file('image')->store('public/restaurants');
        $restaurant->image= basename($image);
    } else {
        $restaurant->image= basename('');
    }

    $restaurant->save();

    return redirect()->route('admin.restaurants.index')->with('flash_message','店舗を登録しました。');
  }

public function edit(Restaurant $restaurant) {
           return view('admin.restaurants.edit', compact('restaurant'));
           }

public function update(Request $request, Restaurant $restaurant) {
    $request->validate([
        'name'=>'required',
        'image'=>'image|max:2048',
        'description'=>'required',
        'lowest_price'=>'required|numeric|min:0|lte:highest_price',
        'highest_price'=>'required|numeric|min:0|gte:lowest_price',
        'postal_code'=>'required|digits:7',
        'address'=>'required',
        'opening_time'=>'required|before:closing_time',
        'closing_time'=>'required|after:opening_time',
        'seating_capacity'=>'required|numeric|min:0',
    ]);


    $restaurant->name = $request->input('name');
    $restaurant->description = $request->input('description');
    $restaurant->lowest_price = $request->input('lowest_price');
    $restaurant->highest_price = $request->input('highest_price');
    $restaurant->postal_code = $request->input('postal_code');
    $restaurant->address = $request->input('address');
    $restaurant->opening_time = $request->input('opening_time');
    $restaurant->closing_time = $request->input('closing_time');
    $restaurant->seating_capacity = $request->input('seating_capacity');

    if ($request->hasFile('image')) {
        $image= $request->file('image')->store('public/restaurants');
        $restaurant->image= basename($image);
    } else {
        $restaurant->image='';
    }

    $restaurant->update();

    return redirect()->route('admin.restaurants.show',$restaurant)->with('flash_message','店舗を編集しました。');
  }

public function destroy(Restaurant $restaurant) {
    $restaurant->delete();

    return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}



  


