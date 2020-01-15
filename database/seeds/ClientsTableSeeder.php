<?php

use App\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new Client([
            'name' => 'Paulo Santana BJJ',
            'affiliation' => 'Lucas Lepri Association',
            'address1' => '1606 South Stratford Road',
            'address2' => 'Suite C',
            'city' => 'Winston-Salem',
            'state' => 'NC',
            'zip' => '27103',
            'country' => 'USA',
        ]);
        $user->save();
    }
}
