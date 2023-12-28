<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Basic Roll

        $admin = new Role();
        $admin->name         = 'admin';
        $admin->display_name = 'Project Admin';
        $admin->description  = 'User is the admin of the project';
        $admin->save();

        //Add User With Role


        $Admin = New App\User();
        $Admin->user_name = "Admin";
        $Admin->email = "admin@gmail.com";
        $Admin->api_token = sha1(time());
        $Admin->phone = "1234567890";
        $Admin->location = "Durgapur";
        $Admin->password = bcrypt("123456");
        $Admin->latitude = '10.1010';
        $Admin->longitude = '12.1010';
        $Admin->status = 'Active';
        $Admin->save();
        $Admin->attachRole(1);

    }
}
