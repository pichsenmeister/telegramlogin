<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('app_id')->unsigned();
            $table->integer('auth_id')->unsigned();
            $table->string('code')->unique();
            $table->timestamps();

            $table->foreign('app_id')
                ->references('id')->on('apps')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('auth_id')
                ->references('id')->on('auths')
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
        Schema::drop('codes');
    }
}
