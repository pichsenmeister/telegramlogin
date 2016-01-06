<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'auths';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id', 'telegram_user_id', 'email'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['telegram_id', 'telegram_user_id', 'app_id', 'updated_at'];

    public function app()
    {
        return $this->belongsTo('App\App');
    }

    public function telegramUser()
    {
        return $this->belongsTo('App\TelegramUser');
    }

    public static function findByAppAndTelegramUser($app, $telegramUser)
    {
        return Auth::where('app_id', '=', $app->id)
            ->where('telegram_user_id', '=', $telegramUser->id)
            ->firstOrFail();
    }

    public static function findByAccessToken($accessToken)
    {
        return Auth::where('access_token', '=', $accessToken)
            ->firstOrFail();
    }

}
