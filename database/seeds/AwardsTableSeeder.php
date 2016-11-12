<?php

use Illuminate\Database\Seeder;

use Imperial\Simp\Award;
use Imperial\Simp\AwardType;

class AwardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $awards = json_decode(Storage::get('lists/awards.json'), true);

        foreach ($awards as $award) {

            $awardType = AwardType::where('abbrev', $award['type'])->first();

            $awardModel = $awardType->awards()->updateOrCreate([
                'abbrev' => $award['abbrev'],
            ], array_except($award, ['type']));
        }
    }
}
