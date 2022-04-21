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
            $table->unsignedBigInteger("publisher")->comment("发布者id");
            $table->string("name")->comment("发布者名称");
            $table->string("title")->comment("标题");
            $table->json("label")->comment("标签");
            $table->string("topic")->nullable()->comment("参与话题");
            $table->string("content");
            $table->integer( "top")->default(0)->comment("置顶");
            $table->double("score")->index()->default(0)->comment("排序分值");
            $table->integer("views")->default(0)->comment("浏览量");
            $table->integer("collections")->default(0)->comment("被收藏次数");
            $table->integer("like")->default(0)->comment("赞数");
            $table->integer("status")->default(0)->comment("0待审核 1上架 2下架");
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
