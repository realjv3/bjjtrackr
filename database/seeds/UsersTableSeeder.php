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
        User::create([
            'name' => 'John Verity',
            'email' => 'jverity@fatguymedia.com',
            'password' => bcrypt('password'),
            'belt' => 3,
            'stripes' => 1,
            'client_id' => 1,
            'start_date' => '2009-12-19',
        ]);

        User::create([
            'name' => 'Paulo Santana',
            'email' => 'paulo_jiujitsu100@hotmail.com',
            'password' => bcrypt('password'),
            'belt' => 5,
            'stripes' => 0,
            'client_id' => 1,
        ]);
    }
}
