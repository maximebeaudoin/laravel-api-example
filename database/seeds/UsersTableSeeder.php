<?php

use App\Domain\User\Entity\User;
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

        // Create users
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

        // Create users personal access token
        $accessTokens = [
            [
                'id' => 1,
                'tokenable_type' => User::class,
                'tokenable_id' => 1,
                // Authorization: Bearer IrgPHM5PBQcYkGCLpWxjvyhP28n3ElXkdPAYKfL6IPYasQurjmmN9roiG79xZ7ahZNGajkCEmTVUTNPM
                'token' => '6206ab2312ab4cf2df25517286626f458908c27b80d0b52a37734e9c344a84a1',
                'abilities' => '["*"]',
                'name' => 'default-token',
                'created_at' => '2020-03-27 14:46:17',
                'updated_at' => '2020-03-27 14:46:27'
            ]
        ];

        DB::table("personal_access_tokens")->insert($accessTokens);
    }
}
