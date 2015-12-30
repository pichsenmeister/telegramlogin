<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auths', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('app_id')->unsigned();
            $table->integer('telegram_user_id')->unsigned();
            $table->string('email');
            $table->string('access_token')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('app_id')
                ->references('id')->on('apps')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('telegram_user_id')
                ->references('id')->on('telegram_users')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auths');
    }
}
