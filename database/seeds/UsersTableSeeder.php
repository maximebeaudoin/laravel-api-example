<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();;

        $users = [
            [
                'id' => 1,
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => app('hash')->make('secret'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table("users")->insert($users);
    }
}
