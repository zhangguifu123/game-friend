<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('managers')->insert(
            [
                [
		    'name'     => '爱着雄雄的小华华',
                    'phone'    => '12345678',
                    'password' => Hash::make('12345678'),
                    'department' => '华哥yyds',
                    'level'   => 0,
                    'api_token' => Str::random(60),
                ]
            ]);

        DB::table('users')->insert(
            [
                [
                    'openid' => '1',
                    'name'   => '张桂福',
                    'avatar' => '1',
                ],
                [
                    'openid' => '2',
                    'name'   => '谢正华',
                    'avatar' => '1',
                ],
                [
                    'openid' => '3',
                    'name'   => '张三',
                    'avatar' => '1',
                ]
            ]);
    }
}
