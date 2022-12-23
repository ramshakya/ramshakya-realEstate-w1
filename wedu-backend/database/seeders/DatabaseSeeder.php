<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $data = array(
            'name' => "super admin",
            'email' => "super@gmail.com",
            'password' =>  Hash::make(12345678),
            'type' =>  1
        );
        User::updateOrCreate($data);
        $data = array(
            'name' => "admin",
            'email' => "admin@gmail.com",
            'password' =>  Hash::make(12345678),
            'type' =>  2
        );
        User::updateOrCreate($data);
        $data = array(
            'name' => "user",
            'email' => "user@gmail.com",
            'password' =>  Hash::make(12345678),
            'type' =>  3
        );
        User::updateOrCreate($data);
    }
}
