<?php

use Illuminate\Database\Seeder;

use Imperial\Simp\Specification;
use Imperial\Simp\Programme;
use Imperial\Simp\ProgrammeType;
use Imperial\Simp\Department;
use Imperial\Simp\Award;
use Imperial\Simp\Calendar;

class OSSProgrammesTableSeeder extends AbstractJsonTableSeeder
{
    protected $calendar;

    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $programmes = $this->data('programmes');
        $this->calendar = Calendar::where('type', 'year')->where('year', '2016/17')->first();

        foreach ($programmes as $programme) {
            $this->makeProgramme($programme);
        }
    }

    protected function makeProgramme($row)
    {
        $programme = Programme::firstOrCreate(['oss_code' => $row['oss_code']]);

        $programme->oss_title = $row['oss_title'];

        $codes = explode('|', $row['department_codes']);
        $names = explode('|', $row['departments']);

        if (count($codes) == count($names)) {
            $departments = array_combine($codes, $names);

            foreach ($departments as $deptCode => $deptName) {
                if ($deptCode == 'DLS') $deptCode = 'LS';
                if ($department = Department::firstOrCreate(['oss_code' => $deptCode], ['name' => $deptName])) {
                    $programme->departments()->syncWithoutDetaching([$department->getKey()]);
                }
            }
        }
        else {
            foreach ($codes as $deptCode) {
                if ($deptCode == 'DLS') $deptCode = 'LS';
                if ($department = Department::where('oss_code', $deptCode)->first()) {
                    $programme->departments()->syncWithoutDetaching([$department->getKey()]);
                }
                else {
                    dd('department_codes:'.$row['oss_code'], $codes);
                }
            }
        }



        if ($award = $this->findAward($row['award'])) {
            $programme->award()->associate($award);
        }

        if ($award = $this->findAward($row['award'])) {
            $programme->award()->associate($award);
        }

        $programme->calendar()->associate($this->calendar);

        $programme->level = $row['level'];

        $programme->mode = $row['mode'];
        $programme->joint_mode = $row['joint_mode'];

        if ($programmeType = ProgrammeType::where('oss_code', $row['programme_type'])->first()) {
            $programme->programmeType()->associate($programmeType);
        }
        else {
            dd('programme_type:'.$row['oss_code'], $row['programme_type']);
        }

        if ($durationMeasure = $this->findDurationMeasure($row['duration'])) {
            $programme->fill($durationMeasure);
        }

        if ($durationMeasure = $this->findDurationMeasure($row['joint_duration'], 'joint_')) {
            $programme->fill($durationMeasure);
        }

        $programme->flags = [
            'taught'            => $row['taught'],
            'entry'             => $row['entry'],
            'industry'          => $row['industry'],
            'management'        => $row['management'],
            'year_abroad'       => $row['year_abroad'],
            'research'          => $row['research'],
            'taught_programmes' => $row['taught_programmes'],
        ];

        $programme->save();

        return $programme;
    }

    protected function findAward($text)
    {
        if ($text) {
            $award = Award::whereNames($text)->get();

            if ($award->count() == 1) {
                return $award->first();
            }
            else {
                dd('award', $text);
            }
        }
    }

    protected function findDurationMeasure($text, $joint = null)
    {
        if ($text) {
            if (preg_match('/(?<duration>\d+)\s*(?<measure>[a-z]*)/i', $text, $match)) {
                return [
                    $joint.'duration' => $match['duration'],
                    $joint.'measure'  => strtoupper($match['measure']) ?: 'Y',
                ];
            }
        }
    }

}
