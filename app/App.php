<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'apps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'redirect_url', 'client_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public static function findByClientId($clientId)
    {
        return App::where('client_id', '=', $clientId)
            ->firstOrFail();
    }

    public static function findByClientIdAndClientSecret($clientId, $clientSecret)
    {
        return App::where('client_id', '=', $clientId)
            ->where('client_secret', '=', $clientSecret)
            ->firstOrFail();
    }

    public static function findByTelegramId($telegramId)
    {
        return App::join('auths', 'auths.app_id', '=', 'apps.id')
            ->where('auths.telegram_id', '=', $telegramId)
            ->where('auths.active', '=', true)
            ->groupBy('apps.id')
            ->get(array('apps.*'));
    }
}
