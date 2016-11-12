<?php

use Imperial\Simp\Campus;

class CampusesTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $campuses = $this->data('campuses');

        foreach ($campuses as $campus) {

            $campusModel = Campus::updateOrCreate([
                'name' => $campus['name'],
            ], $campus);
        }
    }
}
