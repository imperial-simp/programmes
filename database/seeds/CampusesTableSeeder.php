<?php

use Illuminate\Database\Seeder;

use Imperial\Simp\Campus;

class CampusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campuses = json_decode(Storage::get('lists/campuses.json'), true);

        foreach ($campuses as $campus) {

            $campusModel = Campus::updateOrCreate([
                'name' => $campus['name'],
            ], $campus);
        }
    }
}
