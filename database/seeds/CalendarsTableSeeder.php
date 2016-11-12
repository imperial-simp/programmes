<?php

use Illuminate\Database\Seeder;

use Imperial\Simp\Calendar;

class CalendarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $calendars = json_decode(Storage::get('lists/calendars.json'), true);

        foreach ($calendars as $calendar) {

            $calendarModel = calendar::updateOrCreate([
                'name' => $calendar['name'],
            ], $calendar);
        }
    }
}
