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
        //else if(starts_with($message['text'], '/cancel'))
            // do nothing
        else if(starts_with($message['text'], '/list'))
            $this->listApps($message);



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
        }
        $auth->access_token = generate_access_token();
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
        if($token->query_string)
            $url .= '&'.$token->query_string;

        $success = false;
        $trys = 0;
        while(!$success && $trys < 5) {
            $text = 'Please click this link to finish your signup at *'.$app->name.'*: ';
            $text .= $url;
            $tgUrl = 'https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?text='.$text.'&chat_id='.$auth->telegram_id;
            $success = json_decode(file_get_contents($tgUrl), true)['ok'];
            sleep(1);
            $trys++;
        }

        $token->delete();
    }

    private function listApps($message)
    {
        $key = trim(str_replace('/start', '', $message['text']));
        $telegramId = $message['from']['id'];

        $apps = App::findByTelegramId($telegramId);
        if(count($apps)) {
            $text = 'Here are your active apps:'.PHP_EOL;
            $count = 1;
            foreach($apps as $a) {
                $text .= $count.'. '.$a->name;
                if($a->website)
                    $text .= ' ('.$a->website.')';
                $text .= PHP_EOL;
                $count++;
            }
            $text .= PHP_EOL.'';
        } else {
            $text = 'You have no active apps.';
        }

        $tgUrl = 'https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage?text='.urlencode($text).'&chat_id='.$telegramId;
        \Log::debug(file_get_contents($tgUrl));
    }
}
