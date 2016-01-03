<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\App;
use App\Code;
use App\TelegramUser;
use App\User;
use Auth;

class UserController extends Controller
{

    public function login(Requests\LoginRequest $request)
    {
        $user = $this->getUser($request->input('code'), $request->input('state'));
        Auth::login($user);
        return redirect('dashboard');
    }

    private function getUser($code, $state)
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
            app()->abort($result['error'], $result['description']);
        if(array_key_exists('active', $result) && !$result['active'])
            app()->abort($result['error'], $result['description']);

        $tg = TelegramUser::findByTelegramId($result['telegram_user']['telegram_id']);
        if($tg->status != $state)
            app()->abort(403, 'Invalid state.');

        $tg->status = 'access_granted';
        $tg->save();    

        try {
            $user = User::findByTelegramId($result['telegram_user']['telegram_id']);
        } catch (ModelNotFoundException $e) {
            $user = new User();
            $user->email = $result['email'];
            $user->telegram_id = $result['telegram_user']['telegram_id'];
        }
        $user->access_token = $result['access_token'];
        $user->name = $result['telegram_user']['name'];
        $user->username = $result['telegram_user']['username'];
        $user->save();

        return $user;
    }






}
