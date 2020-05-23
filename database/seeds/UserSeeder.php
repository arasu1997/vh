<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['id' => 1, 'name' => 'Admin User', 'email' => 'admin@gmail.com', 'role' => ROLE_ADMIN, 'password' => Hash::make('password'),],
        ];

        DB::table('users')->insert($users);
    }
}
