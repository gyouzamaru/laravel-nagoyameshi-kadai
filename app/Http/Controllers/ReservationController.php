<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index() {
        $user = Auth::user();
        $reservations = Reservation::where('user_id', $user->id)
        ->orderBy('reserved_datetime', 'desc') 
        ->paginate(15); 

        return view('reservations.index', compact('reservations'));
    }

    public function create(Restaurant $restaurant) {
        return view('reservations.create', compact('restaurant'));
    }

    public function store(Request $request, Reservation $reservation, Restaurant $restaurant) {
        $request->validate([
            'reservation_date'=>'required|date_format:Y-m-d',
            'reservation_time'=>'required|date_format:H:i',
            'number_of_people'=>'required|numeric|between:1,50',
        ]);

      $reserved_datetime = $request->reservation_date . ' ' . $request->reservation_time;

      $reservation = new Reservation();
      $reservation->reserved_datetime = $reserved_datetime;
      $reservation->number_of_people = $request->number_of_people;
      $reservation->restaurant_id = $restaurant->id;
      $reservation->user_id = Auth::id();
      $reservation->save();

        return redirect()->route('reservations.index')->with('flash_message','予約が完了しました。');
    }

    public function destroy(Reservation $reservation) {
        if($reservation->user_id !== Auth::id()) {
          return redirect()->route('reservations.index')->with('error_message','不正なアクセスです。'); 
         }
           
         $reservation->delete();
          
          return redirect()->route('reservations.index')->with('flash_message', '予約をキャンセルしました。');
        }
}
