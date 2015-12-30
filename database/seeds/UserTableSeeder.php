<?php

use Illuminate\Database\Seeder;
use App\App;
use App\Auth;
use App\TelegramUser;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $access_token = generate_access_token();
        $email = generate_email().'@telegramlogin.com';

        $user = new User();
        $user->name = 'david pichsenmeister';
        $user->username = 'pichsenmeister';
        $user->email = $email;
        $user->telegram_id = 41478911;
        $user->access_token = $access_token;
        $user->save();

        $app = new App();
        $app->user_id = $user->id;
        $app->name = 'Telegram Login';
        $app->client_id = 314159265;
        $app->client_secret = generate_client_secret();
        $app->website = 'https://telegramlogin.com';
        if(app()->environment('production'))
            $app->redirect_url = 'https://telegramlogin.com/login';
        else
            $app->redirect_url = 'http://tglogin.app/login';
        $app->save();

        $tg = new TelegramUser();
        $tg->telegram_id = 41478911;
        $tg->name = 'david pichsenmeister';
        $tg->username = 'pichsenmeister';
        $tg->save();

        $auth = new Auth();
        $auth->app_id = $app->id;
        $auth->telegram_user_id = $tg->id;
        $auth->email = $email;
        $auth->access_token = $access_token;
        $auth->save();
    }
}
