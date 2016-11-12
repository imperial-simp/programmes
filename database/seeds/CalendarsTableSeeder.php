<?php

use Imperial\Simp\Calendar;

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

        foreach ($calendars as $calendar) {

            $calendarModel = calendar::updateOrCreate([
                'name' => $calendar['name'],
            ], $calendar);
        }
    }
}
