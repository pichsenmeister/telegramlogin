<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\App;
use App\Auth;
use App\Code;
use App\TelegramUser;
use App\Token;

class TelegramController extends Controller
{
    public function receive(Request $request, $token)
    {
        if($token != env('WEBHOOK_TOKEN'))
            app()->abort(401, 'This is not the site you are looking for!');

        $message = $request->input('message');

        if(!array_key_exists('text', $message))
            app()->abort(200, 'Missing command');

        if(starts_with($message['text'], '/start'))
            $this->start($message);
        else if(starts_with($message['text'], '/cancel'))
            $this->cancel($message);
        else if(starts_with($message['text'], '/list'))
            $this->listApps($message);
        else if(starts_with($message['text'], '/revoke'))
            $this->revoke($message);
        else if(starts_with($message['text'], '/help'))
            $this->help($message);
        else
            $this->commandReply($message);

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
            $tg = TelegramUser::findByTelegramId($telegramId);
        } catch (ModelNotFoundException $e) {
            $tg = new TelegramUser();
            $tg->telegram_id = $telegramId;
        }
        $tg->name = $telegramName;
        $tg->username = $username;
        if($app->client_id == 314159265) {
            $tg->status = str_replace('state=', '', $token->query_string);
        } else {
            $tg->status = 'access_granted';
        }
        $tg->save();

        try {
            $auth = Auth::findByAppAndTelegramUser($app, $tg);
        } catch (ModelNotFoundException $e) {
            $auth = new Auth();
            $auth->app_id = $app->id;
            $auth->telegram_user_id = $tg->id;
            $auth->email = generate_email().'-'.$app->id.'-'.$tg->id.'@telegramlogin.com';
        }
        $auth->access_token = generate_access_token();
        $auth->active = true;
        $auth->save();

        $code = Code::create(array(
            'app_id' => $app->id,
            'auth_id' => $auth->id,
            'code' => generate_code()
        ));

        $url = $app->redirect_url.'?code='.$code->code;
        if($token->query_string)
            $url .= '&'.$token->query_string;

        $text = 'Please click this link to finish your signup at *'.$app->name.'*: '.PHP_EOL;
        $text .= '[Click here]('.$url.')';

        $params = array(
            'text' => $text,
            'chat_id' => $telegramId
        );

        $success = false;
        $trys = 0;
        while(!$success && $trys < 5) {
            $success = $this->send($params)['ok'];
            sleep(1);
            $trys++;
        }

        $token->delete();
    }

    private function cancel($message)
    {
        $telegramId = $message['from']['id'];
        $tg = TelegramUser::findByTelegramId($telegramId);
        $tg->status = 'cancel';
        $tg->save();

        $params = array(
            'text' => 'Operation cancelled.',
            'chat_id' => $telegramId,
            'reply_markup' => json_encode(['hide_keyboard' => true])
        );

        $this->send($params);
    }

    private function listApps($message)
    {
        $telegramId = $message['from']['id'];

        $tg = TelegramUser::findByTelegramId($telegramId);
        $tg->status = 'list_apps';
        $tg->save();
        $apps = App::findByTelegramUser($tg);
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

        $params = array(
            'text' => $text,
            'chat_id' => $telegramId
        );

        $this->send($params);
    }

    private function revoke($message)
    {
        $telegramId = $message['from']['id'];

        $tg = TelegramUser::findByTelegramId($telegramId);
        $tg->status = 'revoke_access';
        $tg->save();
        $apps = App::findByTelegramUser($tg);

        $keyboard = array();
        foreach($apps as $a) {
            $keyboard[] = array('['.$a->client_id.'] - '.$a->name);
        }

        $markup = array(
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        );

        $text = 'Select an app to revoke access or type /cancel to cancel this operation:'.PHP_EOL;
        $params = array(
            'text' => $text,
            'chat_id' => $telegramId,
            'reply_markup' => json_encode($markup)
        );

        $this->send($params);
    }

    private function help($message)
    {
        $telegramId = $message['from']['id'];

        $tg = TelegramUser::findByTelegramId($telegramId);
        $tg->status = 'help';
        $tg->save();

        $text = 'Please check our [FAQs](https://telegramlogin.com/faq) for help.'.PHP_EOL;
        $params = array(
            'text' => $text,
            'chat_id' => $telegramId
        );

        $this->send($params);
    }

    private function commandReply($message)
    {
        $telegramId = $message['from']['id'];
        $tg = TelegramUser::findByTelegramId($telegramId);

        $params = array(
            'chat_id' => $telegramId
        );

        if($tg->status == 'revoke_access') {
            $clientId = preg_replace('/[^0-9,.]/', '', $message['text']);
            try {
                $app = App::findByClientId($clientId);
                $auth = Auth::findByAppAndTelegramUser($app, $tg);
                $auth->active = false;
                $auth->save();
                $text = 'Access to this app has been revoked.';
                $tg->status = 'access_revoked';
                $params['reply_markup'] = json_encode(['hide_keyboard' => true]);
            } catch(ModelNotFoundException $e) {
                $text = 'Unknown app. Please choose an app from the given list:';
            }
        } else {
            $text = 'Unknown command.';
            $tg->status = 'unknown_command';
            $params['reply_markup'] = json_encode(['hide_keyboard' => true]);
        }

        $tg->save();

        $params['text'] = $text;

        $this->send($params);
    }


    private function send($params)
    {
        $params['disable_web_page_preview'] = true;
        $params['parse_mode'] = 'Markdown';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'.env('BOT_TOKEN').'/sendMessage');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $json = curl_exec($ch);
        return json_decode($json, true);
    }
}
