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
        $updateId = $request->input('update_id');

        $tg = $this->getTelegramUser($message);

        try {
            TelegramUser::findByTelegramIdAndUpdateId($tg->id, $updateId);
            app()->abort(200, 'Message already received');
        } catch(ModelNotFoundException $e) {
            $tg->update_id = $updateId;
            $tg->save();

            if(!$tg->avatar)
                $this->getAvatar($tg);

            return $this->execute($message, $tg);
        } catch (\Exception $e) {
            \Log::error($e);
            app()->abort(200);
        }
    }

    private function execute($message, $tg) {
        if(!array_key_exists('text', $message))
            app()->abort(200, 'Missing command');

        if(starts_with($message['text'], '/start'))
            $this->start($message, $tg);
        else if(starts_with($message['text'], '/cancel'))
            $this->cancel($message, $tg);
        else if(starts_with($message['text'], '/list'))
            $this->listApps($message, $tg);
        else if(starts_with($message['text'], '/revoke'))
            $this->revoke($message, $tg);
        else if(starts_with($message['text'], '/help'))
            $this->help($message, $tg);
        else
            $this->commandReply($message);

        return response()->json('', 200);
    }

    private function start($message, $tg) {
        $key = trim(str_replace('/start', '', $message['text']));

        $token = Token::findByToken($key);
        $app = App::findOrFail($token->app_id);

        try {
            $auth = Auth::findByAppAndTelegramUser($app, $tg);
        } catch (ModelNotFoundException $e) {
            $auth = new Auth();
            $auth->app_id = $app->id;
            $auth->telegram_user_id = $tg->id;
            $auth->email = 'a'.$app->id.'t'.$tg->id.'-'.generate_email().'@telegramlogin.com';
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
            'chat_id' => $tg->telegram_id
        );
        $this->send($params);
        $token->delete();

        if($app->client_id == 314159265) {
            $tg->status = str_replace('state=', '', $token->query_string);
        } else {
            $tg->status = 'access_granted';
        }
        $tg->save();
    }

    private function cancel($message, $tg)
    {
        $tg->status = 'operation_cancelled';
        $tg->save();

        $params = array(
            'text' => 'Operation cancelled.',
            'chat_id' => $tg->telegram_id,
            'reply_markup' => json_encode(['hide_keyboard' => true])
        );
        $this->send($params);
    }

    private function listApps($message, $tg)
    {
        $tg->status = 'list_apps';
        $tg->save();

        $apps = App::findByTelegramUser($tg);
        \Log::debug($apps);
        if(count($apps) > 0) {
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
            'chat_id' => $tg->telegram_id
        );

        $this->send($params);
    }

    private function revoke($message, $tg)
    {
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
            'chat_id' => $tg->telegram_id,
            'reply_markup' => json_encode($markup)
        );
        $this->send($params);
    }

    private function help($message, $tg)
    {
        $tg->status = 'help';
        $tg->save();

        $text = 'Please check our [FAQs](https://telegramlogin.com/faq) for help. If you need further assistance, please contact @TgLoginSupport\_Bot.'.PHP_EOL;
        $params = array(
            'text' => $text,
            'chat_id' => $tg->telegram_id
        );

        $this->send($params);
    }

    private function commandReply($message, $tg)
    {
        $params = array(
            'chat_id' => $tg->telegram_id
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

    private function getTelegramUser($message) {
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
            $tg = TelegramUser::firstOrNew(array(
                'telegram_id' => $telegramId
            ));
        }
        $tg->name = $telegramName;
        if(isset($username))
            $tg->username = $username;
        $tg->save();
        return $tg;
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

    private function getAvatar($tg)
    {
        $json = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/getUserProfilePhotos?limit=1&user_id='.$tg->telegram_id);
        $response = json_decode($json, true)['result'];

        if(!array_key_exists('photos', $response) || count($response['photos']) == 0)
            return null;

        $file = $response['photos'][0][1];
        if(!array_key_exists('file_path', $file)) {
            $json = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/getFile?file_id='.$file['file_id']);
            $response = json_decode($json, true)['result'];
            $file = $response;
        }
        if(!array_key_exists('file_path', $file)) {
            return null;
        }

        $path = $file['file_path'];

        $split = explode('.', $path);
        $id = $file['file_id'].'.'.end($split);

        $content = file_get_contents('https://api.telegram.org/file/bot'.env('BOT_TOKEN').'/'.$path);
        $localPath = env('AVATAR_DIR').'/'.$id;
        $result = file_put_contents($localPath, $content);

        if($result) {
            $tg->avatar = $localPath;
            $tg->save();
        }
    }
}
