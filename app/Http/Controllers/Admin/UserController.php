<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) 
    {
        $keyword=$request->keyword;

        if($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")->orWhere('kana', 'like', "%{$keyword}%")
            ->paginate(15);
            $total = $users->total();
        } else {
            $users = User::paginate(15);
            $total=0;
        }

        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }

     public function show(User $user)
     {
         return view('admin.users.show', compact('user'));
     }
}
