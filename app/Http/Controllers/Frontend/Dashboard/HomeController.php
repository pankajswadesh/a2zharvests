<?php

namespace App\Http\Controllers\Frontend\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function unauthorized(){
        return view('frontend.errors.401');
    }

    public function forbidden(){
        return view('frontend.errors.403');
    }

    public function notfound(){
        return view('frontend.errors.404');
    }

    public function server_error(){
        return view('frontend.errors.500');
    }
}
