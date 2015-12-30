<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'telegram_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['telegram_id', 'name', 'username'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['id', 'status', 'created_at', 'updated_at'];

    public static function findByTelegramId($telegramId)
    {
        return TelegramUser::where('telegram_id', '=', $telegramId)
            ->firstOrFail();
    }

}
