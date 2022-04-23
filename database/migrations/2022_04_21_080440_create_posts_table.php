<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string("publisher")->comment("发布者id");
            $table->string("name")->comment("发布者名称");
            $table->string("title")->comment("标题");
            $table->string("theme")->comment("主题");
            $table->string("level")->comment("等级");
            $table->string("content")->comment("内容");
            $table->string('img')->comment('图片链接');
            $table->integer("views")->default(0)->comment("浏览量");
            $table->integer("collections")->default(0)->comment("被收藏次数");
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
        Schema::dropIfExists('posts');
    }
}
