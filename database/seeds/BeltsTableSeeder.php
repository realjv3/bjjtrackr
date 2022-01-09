<?php

use App\Models\Belt;
use Illuminate\Database\Seeder;

class BeltsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Belt::create([
            'belt' => 'White',
        ]);
        Belt::create([
            'belt' => 'Blue',
        ]);
        Belt::create([
            'belt' => 'Purple',
        ]);
        Belt::create([
            'belt' => 'Brown',
        ]);
        Belt::create([
            'belt' => 'Black',
        ]);
    }
}
