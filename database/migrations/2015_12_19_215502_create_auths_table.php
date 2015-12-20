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
            $table->bigInteger('telegram_id');
            $table->string('email');
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('access_token')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('app_id')
                ->references('id')->on('apps')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->index('telegram_id');
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
