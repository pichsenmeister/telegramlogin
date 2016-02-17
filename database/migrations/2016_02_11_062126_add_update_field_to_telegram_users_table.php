<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\TelegramUser;
use App\User;

class AddUpdateFieldToTelegramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            $table->bigInteger('update_id')->default(0);
            $table->string('avatar')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
        });

        $all = TelegramUser::all();
        foreach($all as $tg) {
            try {
                $json = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/getUserProfilePhotos?limit=1&user_id='.$tg->telegram_id);
                $response = json_decode($json, true)['result'];

                if(array_key_exists('photos', $response) && count($response['photos']) > 0) {
                    $file = $response['photos'][0][1];
                    if(!array_key_exists('file_path', $file)) {
                        $json = file_get_contents('https://api.telegram.org/bot'.env('BOT_TOKEN').'/getFile?file_id='.$file['file_id']);
                        $response = json_decode($json, true)['result'];
                        $file = $response;
                    }
                    if(!array_key_exists('file_path', $file)) {
                        throw new \Exception();
                    }

                    $path = $file['file_path'];
                    $split = explode('.', $path);
                    $id = $file['file_id'].'.'.end($split);

                    $content = file_get_contents('https://api.telegram.org/file/bot'.env('BOT_TOKEN').'/'.$path);
                    $localPath = env('AVATAR_LOCAL_DIR').'/'.$id;
                    $result = file_put_contents($localPath, $content);

                    if($result) {
                        $tg->avatar = str_replace(env('AVATAR_LOCAL_DIR'), env('AVATAR_DIR'), $localPath);
                        $tg->save();

                        $user = User::where('telegram_id', '=', $tg->telegram_id)->firstOrFail();
                        $user->avatar = $tg->avatar;
                        $user->save();
                    }
                }
            } catch (\Exception $e) { \Log::error($e); }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram_users', function (Blueprint $table) {
            $table->dropColumn('update_id');
            $table->dropColumn('avatar');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
}
