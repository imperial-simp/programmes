<?php

use Imperial\Simp\ProgrammeType;

class ProgrammeTypesTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $programmeTypes = $this->data('programme_types');

        foreach ($programmeTypes as $programmeType) {
            $programmeTypeModel = ProgrammeType::updateOrCreate([
                'oss_code' => $programmeType['oss_code'],
            ], $programmeType);
        }
    }
}
