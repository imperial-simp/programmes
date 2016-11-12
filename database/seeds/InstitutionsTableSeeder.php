<?php

use Imperial\Simp\Institution;
use Imperial\Simp\Faculty;
use Imperial\Simp\Department;

class InstitutionsTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $institutions = $this->data('institutions');

        foreach ($institutions as $institution) {

            $institutionModel = Institution::updateOrCreate([
                'name' => $institution['name'],
            ], array_except($institution, ['faculties']));

            foreach ($institution['faculties'] as $faculty) {
                $facultyModel = $institutionModel->faculties()->updateOrCreate([
                    'name' => $faculty['name'],
                ], array_except($faculty, ['departments']));

                foreach ($faculty['departments'] as $department) {
                    $department = $facultyModel->departments()->updateOrCreate([
                        'name' => $department['name'],
                    ], $department);
                }
            }
        }
    }
}
