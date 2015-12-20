<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\App;
use App\Auth;
use App\Code;
use App\Token;

class TelegramController extends Controller
{
    public function receive(Request $request, $token)
    {
        if($token != env('WEBHOOK_TOKEN'))
            app()->abort(401, 'This is not the site you are looking for!');

        $message = $request->input('message');

        if(!array_key_exists('text', $message))
            app()->abort(200, 'Missing start command');

        if(starts_with($message['text'], '/start'))
            $this->start($message);


        return response()->json('', 200);
    }

    private function start($message) {
        $key = trim(str_replace('/start', '', $message['text']));

        $token = Token::findByToken($key);
        $app = App::findOrFail($token->app_id);

        $from = $message['from'];
        $telegramId = $from['id'];
        $telegramName = $from['first_name'];
        if(array_key_exists('last_name', $from))
            $telegramName .= ' '.$from['last_name'];
        if(array_key_exists('username', $from))
            $username = $from['username'];

        try {
            $auth = Auth::findByAppAndTelegramId($app, $telegramId);
        } catch (ModelNotFoundException $e) {
            $auth = new Auth();
            $auth->app_id = $app->id;
            $auth->telegram_id = $telegramId;
            $auth->email = $telegramId.'@telegramlogin.com';
            $auth->access_token = generate_access_token();
        }
        $auth->name = $telegramName;
        $auth->username = $username;
        $auth->active = true;
        $auth->save();

        $code = Code::firstOrCreate(array(
            'app_id' => $app->id,
            'auth_id' => $auth->id,
            'code' => generate_code()
        ));

        $url = $app->redirect_url.'?code='.$code->code;

        $success = false;
        $trys = 0;
        while(!$success && $trys < 5) {
            $text = 'Please click this link to finish your signup at *'.$app->name.'*: ';
            $text .= $url;
            $tgUrl = 'https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?text='.urlencode($text).'&parse_mode=Markdown&chat_id='.$auth->telegram_id;
            $success = json_decode(file_get_contents($tgUrl), true)['ok'];
            sleep(2);
            $trys++;
        }

        $token->delete();
    }
}
