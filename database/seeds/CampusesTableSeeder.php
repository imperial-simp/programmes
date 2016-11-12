<?php

use Imperial\Simp\Campus;
use Imperial\Simp\Institution;

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
        $institution = Institution::whereName('Imperial College London')->first();

        foreach ($campuses as $campus) {

            $campusModel = Campus::updateOrCreate([
                'name' => $campus['name'],
            ], $campus);

            $campusModel->institution()->associate($institution)->save();
        }
    }
}
