<?php

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Rank::create([
            'user_id' => 1,
            'belt_id' => 3,
            'stripes' => 1,
            'last_ranked_up' => '2019-06-01',
        ]);
        Rank::create([
            'user_id' => 2,
            'belt_id' => 5,
            'stripes' => 1,
            'last_ranked_up' => '2012-08-02',
        ]);
    }
}
