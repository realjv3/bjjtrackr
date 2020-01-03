<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role(['role' => 'Super Admin']);
        $role->save();

        $role = new Role(['role' => 'Administrator']);
        $role->save();

        $role = new Role(['role' => 'Instructor']);
        $role->save();

        $role = new Role(['role' => 'Student']);
        $role->save();
    }
}
