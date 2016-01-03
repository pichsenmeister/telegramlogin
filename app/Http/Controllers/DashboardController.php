<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\App;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $apps = App::findByUser($user);
        return view('dashboard', ['apps' => $apps]);
    }

    public function profile(Request $request)
    {
        return view('profile', ['user' => $request->user()]);
    }


}
