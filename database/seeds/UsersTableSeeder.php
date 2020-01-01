<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User([
            'name' => 'John Verity',
            'email' => 'jverity@fatguymedia.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);
        $user->save();

        $user = new User([
            'name' => 'Paulo Santana',
            'email' => 'paulo_jiujitsu100@hotmail.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);
        $user->save();

        $user = new User([
            'name' => 'Neil Fernandez',
            'email' => 'neilfernandezdev@gmail.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);
        $user->save();

        $user = new User([
            'name' => 'Lucas Lepri',
            'email' => 'lucas@lepri.com',
            'password' => bcrypt('password'),
            'role_id' => 2,
        ]);
        $user->save();

        $user = new User([
            'name' => 'Teacher',
            'email' => 'teacher@lepri.com',
            'password' => bcrypt('password'),
            'role_id' => 3,
        ]);
        $user->save();

        $user = new User([
            'name' => 'Student',
            'email' => 'student@lepri.com',
            'password' => bcrypt('password'),
            'role_id' => 4,
        ]);
        $user->save();
    }
}
