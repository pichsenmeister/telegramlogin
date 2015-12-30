<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\App;
use App\Code;
use App\User;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function code(Requests\CodeRequest $request)
    {
        try {
            $app = App::findByClientIdAndClientSecret($request->input('client_id'), $request->input('client_secret'));
            $code = Code::findByAppAndCode($app, $request->input('code'));
            $auth = $code->auth()->first();

            $code->delete();

            if(!$auth || !$auth->active) {
                return response()->json(['error' => 401, 'description' => 'No active user found.'], 401);
            } else {
                $auth->telegram_user = $auth->telegramUser()->first();
                //$data = array_merge($tgUser->toArray(), $auth->toArray());
                return response()->json($auth);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 404, 'description' => 'Invalid code.'], 404);
        }
    }

}
