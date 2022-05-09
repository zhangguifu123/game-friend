<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->string('sex')->default('请选择');
            $table->string('age')->default('无');
            $table->string('college')->default('无');
            $table->string('school')->default('无');
            $table->string('specialty')->default('无');
            $table->string('introduction')->default('无');
            $table->integer('status')->default(2);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
