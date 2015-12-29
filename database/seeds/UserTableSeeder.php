<?php

use Illuminate\Database\Seeder;
use App\App;
use App\Auth;
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

        $user = new User();
        $user->name = 'david pichsenmeister';
        $user->username = 'pichsenmeister';
        $user->email = '41478911@telegramlogin.com';
        $user->telegram_id = 41478911;
        $user->access_token = $access_token;
        $user->save();

        $app = new App();
        $app->user_id = $user->id;
        $app->name = 'Telegram Login';
        $app->client_id = generate_client_id();
        $app->client_secret = generate_client_secret();
        $app->website = 'https://telegramlogin.com';
        if(app()->environment('production'))
            $app->redirect_url = 'https://telegramlogin.com/login';
        else
            $app->redirect_url = 'http://tglogin.app/login';
        $app->save();

        $auth = new Auth();
        $auth->app_id = $app->id;
        $auth->telegram_id = 41478911;
        $auth->email = '41478911@telegramlogin.com';
        $auth->name = 'david pichsenmeister';
        $auth->username = 'pichsenmeister';
        $auth->access_token = $access_token;
        $auth->save();
    }
}
