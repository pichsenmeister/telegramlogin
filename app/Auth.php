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
    protected $fillable = ['app_id', 'telegram_id', 'email', 'name',
        'username'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function app()
    {
        return $this->belongsTo('App\App');
    }

    public static function findByAppAndTelegramId($app, $telegramId)
    {
        return Auth::where('app_id', '=', $app->id)
            ->where('telegram_id', '=', $telegramId)
            ->firstOrFail();
    }

}
