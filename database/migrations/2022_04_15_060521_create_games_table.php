<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('publisher')->comment('发布者')->index();
            $table->string('name')->comment('赛事名称');
            $table->string('sign_up_time')->comment('报名时间');
            $table->string('game_time')->comment('比赛时间');
            $table->string('organizer')->comment('主办单位');
            $table->string('subject')->comment('竞赛科目');
            $table->string('type')->comment('竞赛类别');
            $table->string('level')->comment('竞赛级别');
            $table->integer("collections")->default(0)->comment("被收藏次数");
            $table->string('img')->comment('图片链接');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
