<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    protected function view()
    {
        return view('welcome');
    }
}
