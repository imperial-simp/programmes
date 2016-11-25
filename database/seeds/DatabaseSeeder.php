<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(SourcesTableSeeder::class);
        // $this->call(InstitutionsTableSeeder::class);
        // $this->call(CalendarsTableSeeder::class);
        // $this->call(AwardTypesTableSeeder::class);
        // $this->call(ProgrammeTypesTableSeeder::class);
        // $this->call(AwardsTableSeeder::class);
        // $this->call(CampusesTableSeeder::class);
        $this->call(ProgrammesTableSeeder::class);
        // $this->call(OSSProgrammesTableSeeder::class);
        // $this->call(OSSModulesTableSeeder::class);
    }
}
