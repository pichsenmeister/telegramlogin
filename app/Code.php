<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id', 'auth_id', 'code'];

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

    public function auth()
    {
        return $this->belongsTo('App\Auth');
    }

    public static function findByAppAndCode($app, $code)
    {
        return Code::where('app_id', '=', $app->id)
            ->where('code', '=', $code)
            ->firstOrFail();
    }
}
