<?php

use Imperial\Simp\Award;
use Imperial\Simp\AwardType;

class AwardsTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $awards = $this->data('awards');

        foreach ($awards as $award) {

            $awardType = AwardType::where('abbrev', $award['type'])->first();

            $awardModel = $awardType->awards()->updateOrCreate([
                'abbrev' => $award['abbrev'],
            ], array_except($award, ['type']));
        }
    }
}
