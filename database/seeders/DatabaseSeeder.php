<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
                [
                    'phone'    => '123456',
                    'password' => Hash::make('123456'),
                    'department' => '华哥yyds',
                    'level'   => 0,
                    'api_token' => Str::random(60),
                ]
            ]);
    }
}
