<?php

use Illuminate\Database\Seeder;

use Imperial\Simp\Specification;
use Imperial\Simp\Programme;
use Imperial\Simp\Department;
use Imperial\Simp\Award;
use Imperial\Simp\Calendar;
use Imperial\Simp\Module;

class ProgrammesTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $specifications = Specification::whereNotNull('parsed_at')->where('contents', 'NOT LIKE', '%"Errors"%')->get();

        foreach ($specifications as $specification) {
            $information = $specification->contents['Specification']['Programme_Information'];

            if (isset($information['Programme_Code'])) {
                $programmeModel = $this->makeProgramme($specification, $information['Programme_Code'], $information['Programme_Title']);
            }
            elseif (isset($information['Programme_Title_Programme_Code'])) {
                foreach ($information['Programme_Title_Programme_Code'] as $subProgramme) {
                    $programmeModel = $this->makeProgramme($specification, $subProgramme['Code'], $subProgramme['Title']);
                }
            }

        }
    }

    protected function makeProgramme($specification, $code, $title)
    {
        $information = $specification->contents['Specification']['Programme_Information'];

        $programme = Programme::firstOrCreate(['oss_code' => $code]);

        $department = Department::whereNames($information['Department'])->get();

        if ($department->count()) {
            $programme->departments()->syncWithoutDetaching($department->modelKeys());
        }
        else {
            $department = Department::whereNames(implode(' ', (array) $information['Department']))->get();

            if ($department->count() == 1) {
                $programme->departments()->syncWithoutDetaching($department->modelKeys());
            }
            else {
                dd($information['Department'], $department);
            }
        }

        $award = Award::whereNames($information['Awards'])->get();

        if ($award->count() == 1) {
            $award = $award->first();
            $programme->long_title = $award->abbrev . ' ' . $title;
            $programme->award()->associate($award);
        }
        else {
            dd($information['Awards'], $award);
        }

        $calendar = Calendar::where('type', 'year')->where('year', $specification->details['entry_year'])->first();
        $programme->calendar()->associate($calendar);

        switch ($specification->details['level']) {
            case 'Postgraduate':
            $programme->level = 'PG';
            break;
            case 'Undergraduate':
            $programme->level = 'UG';
            break;
            default:
            dd($specification->details['level']);
        }

        $programme->specifications()->syncWithoutDetaching([
            $specification->getKey()
        ]);

        $programme->save();

        if (isset($specification->contents['Specification']['Indicative_Module_List'])) {
            foreach ($specification->contents['Specification']['Indicative_Module_List']['Modules'] as $module) {

                if (@$module['Code']) {
                    if (is_array($module['Code'])) {
                        foreach ($module['Code'] as $code) {
                            $moduleModel = $this->makeModule($specification, $programme, $code, $module['Title'], $module);
                        }
                    }
                    else {
                        $moduleModel = $this->makeModule($specification, $programme, $module['Code'], $module['Title'], $module);
                    }
                }

            }
        }
        else {
            dd(array_keys($specification->contents['Specification']));
        }

        return $programme;
    }

    protected function makeModule($specification, $programme, $code, $title, $contents)
    {
        $module = Module::firstOrCreate(['oss_code' => $code]);

        $module->long_title = $title;

        $module->specifications()->syncWithoutDetaching([
            $specification->getKey() => [
                'ects'              => @$contents['ECTS'],
                'fheq'              => @$contents['FHEQ'],
                'learning_hours'    => @$contents['Learning_Hours'],
                'study_hours'       => @$contents['Study_Hours'],
                'placement_hours'   => @$contents['Placement_Hours'],
                'total_hours'       => @$contents['Total_Hours'],
                'exam_weight'       => @$contents['Written_Exam'],
                'coursework_weight' => @$contents['Coursework'],
                'practical_weight'  => @$contents['Practical'],
            ]
        ]);
        
        $module->programmes()->syncWithoutDetaching([
            $programme->getKey() => [
                'years'          => @$contents['Year'],
                'core'           => @$contents['Core'] ?: false,
                'elective_group' => @$contents['Elective_Group'] ?: null,
            ]
        ]);

        $module->save();
    }
}
