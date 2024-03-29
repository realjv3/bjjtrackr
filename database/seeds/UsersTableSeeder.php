<?php

use App\Models\User;
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
            'client_id' => 1,
            'start_date' => '2009-12-19',
            'active' => true,
        ]);

        User::create([
            'name' => 'Paulo Santana',
            'email' => 'paulo_jiujitsu100@hotmail.com',
            'password' => bcrypt('password'),
            'client_id' => 1,
            'start_date' => '2015-06-01',
            'active' => true,
        ]);
    }
}
