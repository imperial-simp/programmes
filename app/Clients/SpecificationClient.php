<?php

namespace Imperial\Simp\Clients;

use Imperial\Simp\Specification;
use Imperial\Simp\Institution;
use Imperial\Simp\Faculty;
use Imperial\Simp\Department;
use Imperial\Simp\Award;
use Imperial\Simp\Calendar;

use Imperial\Simp\Jobs\DownloadSpecificationJob;
use InvalidArgumentException;

class SpecificationClient extends AbstractClient
{
    protected $faculty;
    protected $department;
    protected $level;
    protected $entries = [];

    protected $calendarModel;
    protected $institutionModel;
    protected $facultyModel;
    protected $departmentModel;

    public function run()
    {
        $this->get();

        $this->institutionModel = Institution::whereName('Imperial College London')->first();

        $this->crawler->filter('.module .fake-h3')->each(function($node) { $this->getFaculty($node); });
    }

    protected function getFaculty($node)
    {
        $this->faculty = $node->text();

        if ($this->institutionModel) {
            $this->facultyModel = $this->institutionModel->faculties()->whereNames($this->faculty)->first();
        }

        $node->nextAll()->filter('.panel-group')->first()->filter('.item')->each(function($node) { $this->getDepartment($node); });
    }

    protected function getDepartment($node)
    {
        $this->department = $node->filter('.item-header')->text();

        if ($this->facultyModel) {
            $this->departmentModel = $this->facultyModel->departments()->whereNames($this->department)->first();
        }

        $node->filter('.item-content table')->each(function($node) { $this->getLevel($node); });
    }

    protected function getLevel($node)
    {
        $this->level = $node->filter('caption')->text();

        $this->getEntries($node);

        $node->filter('tbody tr')->each(function($node) { $this->getProgrammes($node); });
    }

    protected function getEntries($node)
    {
        $this->entries = $node->filter('thead tr:first-child th')->each(function($node) {
            return str_replace(' entry', '', $node->text());
        });
    }

    protected function getProgrammes($node)
    {
        $node->filter('td')->each(function($node, $i) { $this->getProgramme($node, $i); });
    }

    protected function getProgramme($node, $i)
    {
        $title = $node->text();
        $title = str_replace(['[pdf]', '*'], '', $title);
        $title = preg_replace('/\s+/u', ' ', $title);
        $title = trim($title);

        if ($title != 'N/A') {

            $awards = explode(' ', $title);

            if (last($awards) == 'MBA') {
                $award = 'MBA';
            }
            else {
                $award = array_shift($awards);

                if ('PG' == $award) {
                    $award .= array_shift($awards);
                }

                $award = str_replace('MSci', 'MSc', $award);
            }

            if (stripos($award, '/') !== false) {
                $award = explode('/', $award);
            }

            $entryYear = @$this->entries[$i];

            $this->calendarModel = Calendar::where('year', 'LIKE', strstr($entryYear, '/', true).'/%')->where('type', 'year')->first();

            $details = [
                'faculty'    => $this->faculty,
                'department' => $this->department,
                'level'      => $this->level,
                'title'      => $title,
                'award'      => $award,
                'entry_year' => $entryYear,
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

            if ($this->facultyModel) {
                $specification->faculty()->associate($this->facultyModel);
            }

            if ($this->departmentModel) {
                $specification->department()->associate($this->departmentModel);
            }

            if (is_array($award)) {
                if ($awardModel = Award::where('abbrev', array_shift($award))->first()) {
                    $specification->award()->associate($awardModel);
                }

                if ($awardModel = Award::where('abbrev', array_shift($award))->first()) {
                    $specification->jointAward()->associate($awardModel);
                }
            }
            else {
                if ($awardModel = Award::where('abbrev', $award)->first()) {
                    $specification->award()->associate($awardModel);
                }
            }


            if ($this->calendarModel) {
                $specification->calendar()->associate($this->calendarModel);
            }

            if (!$specification->url) {
                try {
                    $url = $node->filter('a[href]')->first()->link()->getUri();

                    $specification->url = $url;
                    $specification->file = basename($url, '.pdf').'_'.$hash.'.pdf';
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
}
