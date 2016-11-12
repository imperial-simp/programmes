<?php

use Illuminate\Database\Seeder;

use Imperial\Simp\AwardType;

class AwardTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $award_types = json_decode(Storage::get('lists/award_types.json'), true);

        foreach ($award_types as $award_type) {

            $awardTypeModel = AwardType::updateOrCreate([
                'name' => $award_type['name'],
            ], $award_type);
        }
    }
}
