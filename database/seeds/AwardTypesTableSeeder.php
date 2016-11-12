<?php

use Imperial\Simp\AwardType;

class AwardTypesTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $award_types = $this->data('award_types');

        foreach ($award_types as $award_type) {

            $awardTypeModel = AwardType::updateOrCreate([
                'name' => $award_type['name'],
            ], $award_type);
        }
    }
}
