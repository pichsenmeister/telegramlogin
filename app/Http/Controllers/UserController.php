<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\App;
use App\Code;
use App\User;

class UserController extends Controller
{

    public function login(Requests\LoginRequest $request)
    {
        $user = $this->getUser($request->input('code'));

        return response()->json($user);
    }

    private function getUser($code)
    {
        $app = App::findOrFail(1);

        $params = array(
            'code' => $code,
            'client_id' => $app->client_id,
            'client_secret' => $app->client_secret
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('URL').'/user');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $json = curl_exec($ch);
        $result = json_decode($json, true);

        if(!array_key_exists('access_token', $result))
            app()->abort(401, 'No active user found.');
        if(array_key_exists('active', $result) && !$result['active'])
            app()->abort(401, 'No active user found.');

        try {
            $user = User::findByAccessToken($result['access_token']);
        } catch (ModelNotFoundException $e) {
            $user = new User();
            $user->email = $result['email'];
            $user->telegram_id = $result['telegram_id'];
            $user->access_token = $result['access_token'];
        }
        $user->name = $result['name'];
        $user->username = $result['username'];
        $user->save();

        return $user;
    }






}
