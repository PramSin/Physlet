<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GlobalController extends Controller
{
    protected function view(Request $request)
    {
        return view('physlet');
    }
}
