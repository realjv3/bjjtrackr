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
        Client::create([
            'name' => 'Paulo Santana BJJ',
            'affiliation' => 'Lucas Lepri Association',
            'address1' => '1606 South Stratford Road',
            'address2' => 'Suite C',
            'city' => 'Winston-Salem',
            'state' => 'NC',
            'zip' => '27103',
            'country' => 'USA',
        ]);
        Client::create([
            'name' => 'R&D Academy of Self Defense',
            'affiliation' => 'Gracie Humaitá',
            'address1' => '4755 Commercial Plaza Dr',
            'address2' => '',
            'city' => 'Winston-Salem',
            'state' => 'NC',
            'zip' => '27104',
            'country' => 'USA',
        ]);
    }
}
