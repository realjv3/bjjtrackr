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
            'belt' => 'purple',
        ]);
        $user->save();

        $user = new User([
            'name' => 'Neil Fernandez',
            'email' => 'neilfernandezdev@gmail.com',
            'password' => bcrypt('password'),
            'belt' => 'purple',
        ]);
        $user->save();
    }
}
