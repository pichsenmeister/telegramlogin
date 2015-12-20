<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\App;
use App\Code;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function code(Requests\AuthRequest $request)
    {
        $app = App::findByClientIdAndClientSecret($request->input('client_id'), $request->input('client_secret'));
        $code = Code::findByAppAndCode($app, $request->input('code'));
        $auth = $code->auth()->first();

        $code->delete();
        
        if(!$auth || !$auth->active)
            return response()->json('No active user found.', 401);
        else
            return response()->json($auth);
    }

}
