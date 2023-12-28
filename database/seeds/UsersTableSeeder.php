<?php

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
        DB::table('users')->insert([
            'user_name' => 'John Doe',
            'phone' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'status' => 'Active',
        ]);
    }
}
