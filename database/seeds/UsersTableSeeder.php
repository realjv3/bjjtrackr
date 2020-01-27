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
            'belt' => 'Purple',
            'stripes' => 1,
            'client_id' => 1,
        ]);
        $user->save();

        $user = new User([
            'name' => 'Paulo Santana',
            'email' => 'paulo_jiujitsu100@hotmail.com',
            'password' => bcrypt('password'),
            'belt' => 'Black',
            'stripes' => 0,
            'client_id' => 1,
        ]);
        $user->save();
    }
}
