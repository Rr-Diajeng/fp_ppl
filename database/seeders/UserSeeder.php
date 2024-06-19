<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('admin'),
                'role' => 'Admin',
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'PIC',
                'email' => 'pic@gmail.com',
                'password' => bcrypt('pic'),
                'role' => 'PIC',
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'password' => bcrypt('user'),
                'role' => 'User',
                'remember_token' => Str::random(10),
            ],
        ];

        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 10; $i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('password'),
                'role' => 'User',
                'remember_token' => Str::random(10),
            ];
        }

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
