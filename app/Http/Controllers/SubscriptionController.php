<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user;

class SubscriptionController extends Controller
{
  public function create() {

    $intent = Auth::user()->createSetupIntent();

    return view('subscription.create', compact('intent'));
  }

  public function store(Request $request) {
    
    $request->user()->newSubscription(
            'premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj'
        )->create($request->paymentMethodId);

    return redirect()->route('home')->with('flash_message','有料プランへの登録が完了しました。');
  }

  public function edit() {
    $user = Auth::user();
    $intent = Auth::user()->createSetupIntent();

    return view('subscription.edit', compact('user','intent'));
  }

  public function update(Request $request) {
    $user = Auth::user();

    $user->updateDefaultPaymentMethod($request->paymentMethodId);

    return redirect()->route('home')->with('flash_message','お支払方法を変更しました。');
  }

  public function cancel() {
   
    return view('subscription.cancel');
  }

  public function destroy(Request $request) {
    $request->user()->subscription('premium_plan')->cancelNow();
        
    return redirect()->route('home')->with('flash_message','有料プランを解約しました。');
   }

}


