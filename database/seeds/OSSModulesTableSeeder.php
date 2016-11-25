<?php

use Imperial\Simp\Module;

class OSSModulesTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = $this->data('modules');

        foreach ($modules as $module) {

            $moduleModel = Module::updateOrCreate([
                'oss_code' => $module['oss_code'],
            ], $module);
        }
    }
}
