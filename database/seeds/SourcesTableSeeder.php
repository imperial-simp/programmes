<?php

use Imperial\Simp\Source;

class SourcesTableSeeder extends AbstractJsonTableSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sources = $this->data('sources');

        foreach ($sources as $source) {

            $sourceModel = Source::updateOrCreate([
                'url' => $source['url'],
            ], $source);
        }
    }
}
