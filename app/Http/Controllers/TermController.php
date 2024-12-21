<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
      $term = Term::first();

      return view('terms.index', compact('term'));
    }
}
