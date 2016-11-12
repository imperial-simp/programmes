<?php

use Imperial\Simp\Calendar;
use Imperial\Simp\Institution;

class CalendarsTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calendars = $this->data('calendars');
        $institution = Institution::whereName('Imperial College London')->first();

        foreach ($calendars as $calendar) {

            $calendarModel = Calendar::updateOrCreate([
                'name' => $calendar['name'],
            ], $calendar);

            $calendarModel->institution()->associate($institution)->save();
        }

        $year = (int) date('Y');
        $calendars = [
            'Academic Year' => 'year',
            'Autumn Term'   => 'term',
            'Spring Term'   => 'term',
            'Summer Term'   => 'term',
        ];

        for ($startYear = $year - 10; $startYear < $year; $startYear++) {

            $endYear = $startYear + 1;
            $years = (string) $startYear.'/'.substr((string) $endYear, -2);

            foreach ($calendars as $name => $type) {
                $calendarModel = Calendar::firstOrCreate([
                    'name' => $name.' '.$years,
                    'year' => $years,
                    'type' => $type,
                ]);

                $calendarModel->institution()->associate($institution)->save();
            }
        }
    }
}
