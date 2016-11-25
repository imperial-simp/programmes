<?php

namespace Imperial\Simp\Clients;

use Imperial\Simp\Calendar;
use Imperial\Simp\Specification;
use Imperial\Simp\Institution;

use Imperial\Simp\Jobs\DownloadSpecificationJob;
use InvalidArgumentException;

class HorizonsModuleClient extends AbstractClient
{
    protected $calendarModel;
    protected $institutionModel;
    protected $fieldOfStudy;
    protected $years;

    public function run()
    {
        $this->get();

        $this->institutionModel = Institution::whereName('Imperial College London')->first();
        $this->calendarModel = Calendar::where('year', '2016/17')->where('type', 'year')->first();

        $this->crawler->filter('#primary-content > .module > .panel-group > .item')->each(function($node) { $this->getFieldOfStudy($node); });
    }

    protected function getFieldOfStudy($node)
    {
        $fieldOfStudy = trim($node->filter('h3.item-header')->first()->text());

        if (preg_match('/(?<Field>.*) for (?<Years>.*) years/', $fieldOfStudy, $match)) {
            $this->fieldOfStudy = $match['Field'];
            $this->years = $match['Years'];
        }

        $node->filter('.item-content > table > tbody > tr')->each(function($node) {
            $this->getModule($node);
        });
    }

    protected function cleanNbsp($text)
    {
        return trim(str_replace('&nbsp;', ' ', htmlentities($text)));
    }

    protected function getModule($node)
    {

        $code = $this->cleanNbsp($node->filter('td')->eq(0)->text());
        $title = $this->cleanNbsp($node->filter('td')->eq(1)->text());
        $ects = $this->cleanNbsp($node->filter('td')->eq(2)->text());
        $duration = $this->cleanNbsp($node->filter('td')->eq(3)->text());
        $terms = $this->cleanNbsp($node->filter('td')->eq(4)->text());

        $details = [
            'level'    => 'Undergraduate',
            'type'     => 'Horizons',
            'code'     => $code,
            'title'    => $title,
            'ects'     => (string)(int) $ects == $ects ? (int) $ects : $ects,
            'duration' => $duration,
            'terms'    => $terms,
            'field'    => $this->fieldOfStudy,
            'years'    => $this->years,
        ];

        $hash = md5(json_encode($details));

        $specification = Specification::firstOrNew(['hash' => $hash]);

        if (!$specification->exists) {
            $specification->title = $title;
            $specification->hash = $hash;
            $specification->details = $details;
        }

        if ($this->sourceModel) {
            $specification->source()->associate($this->sourceModel);
        }

        if ($this->institutionModel) {
            $specification->institution()->associate($this->institutionModel);
        }

        if ($this->calendarModel) {
            $specification->calendar()->associate($this->calendarModel);
        }

        if (!$specification->url) {
            try {
                $url = $node->filter('a[href]')->first()->link()->getUri();

                $specification->url = $url;
                $specification->file = substr(str_slug($title), 0, 30).'_'.$hash.'.html';
            }
            catch (InvalidArgumentException $e) { }
        }

        $specification->save();

        if ($specification->shouldRetrieve())
        {
            dispatch(new DownloadSpecificationJob($specification));
        }
    }
}
