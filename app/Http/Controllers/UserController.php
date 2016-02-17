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

    public function show(Request $request)
    {
        $accessToken = $request->input('access_token');
        try {
            $auth = \App\Auth::findByAccessToken($accessToken);
            $auth->telegram_user = $auth->telegramUser()->first();
            return response()->json($auth);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'No active user found.'], 404);
        }
    }

    public function send(Request $request)
    {
        $accessToken = $request->input('access_token');
        try {
            $auth = \App\Auth::findByAccessToken($accessToken);
            $auth->telegram_user = $auth->telegramUser()->first();

            $app = $auth->app()->first();
            $text = 'Send on behalf of: ['. $app->client_id .'] '. $app->name.PHP_EOL.PHP_EOL;
            $text .= 'Message: '.PHP_EOL;
            $text .= $request->input('text').PHP_EOL.PHP_EOL;
            $text .= 'Note: If you don\'t want to receive further updates from ['. $app->client_id .'] '. $app->name;
            $text .= ', you can revoke access via the /revoke command';

            $params = array(
                'chat_id' => $auth->telegram_user->telegram_id,
                'text' => $text
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $json = curl_exec($ch);
            $result = json_decode($json, true);

            if($result['ok'])
                return response()->json(array('ok' => true));
            else
                return response()->json($result);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'No active user found.'], 404);
        }
    }

    public function login(Requests\LoginRequest $request)
    {
        $user = $this->getUser($request->input('code'), $request->input('state'));
        Auth::login($user);
        return redirect('dashboard');
    }

    private function getUser($code, $state)
    {
        $app = App::findByClientId(314159265);

        $params = array(
            'code' => $code,
            'client_id' => $app->client_id,
            'client_secret' => $app->client_secret
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('URL').'/code');
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
