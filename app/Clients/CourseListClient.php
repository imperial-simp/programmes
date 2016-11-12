<?php

namespace Imperial\Simp\Clients;

use Imperial\Simp\Calendar;
use Imperial\Simp\Specification;
use Imperial\Simp\Institution;
use Imperial\Simp\Faculty;
use Imperial\Simp\Department;
use Imperial\Simp\Award;

use Imperial\Simp\Jobs\DownloadSpecificationJob;
use InvalidArgumentException;

class CourseListClient extends AbstractClient
{
    protected $entryYear;
    protected $level;

    protected $calendarModel;
    protected $institutionModel;
    protected $facultyModel;
    protected $departmentModel;

    public function run()
    {
        $this->get();

        $this->institutionModel = Institution::whereName('Imperial College London')->first();

        if (preg_match('/\d{4}/', $this->crawler->filter('.page-heading h1')->text(), $matches)) {
            $this->entryYear = $matches[0];
            $this->calendarModel = Calendar::where('year', 'LIKE', $this->entryYear.'/%')->where('type', 'year')->first();
        }

        $this->level = $this->crawler->filter('h2#section-title')->text();

        $this->crawler->filter('.page-a-z .courses.primary li.course')->each(function($node) { $this->getProgramme($node); });
    }

    protected function getProgramme($node)
    {
        $department = $node->filter('.type.dept')->first()->text();

        $departmentKey = $department == 'Biomedical Science' ? 'Department of Medicine' : $department;

        $facultyKeys = $this->institutionModel->faculties->pluck('id');

        $this->departmentModel = Department::whereNames($departmentKey)->whereIn('faculty_id', $facultyKeys)->first();

        if ($this->departmentModel)
        {
            $this->facultyModel = $this->departmentModel->faculty;
        }

        $title = $node->filter('h4.title')->first()->text();
        $title = trim($title);

        $fields = [];

        $node->filter('.type:not(.dept)')->each(function($node) use (&$fields) {
            list($key, $value) = explode(':', $node->text());
            $key = trim($key, ': ');
            $value = trim($value, ': ');
            $fields[$key] = $value;
        });

        if ($title) {

            if (isset($fields['Qualification/s'])) {
                $fields['Degree'] = $fields['Qualification/s'];
            }

            if (isset($fields['Degree'])) {
                $award = $fields['Degree'];
                $award = str_replace('MSci', 'MSc', $award);
                $award = str_replace(' ', '', $award);
            }
            else {
                $award = null;
            }

            if (stripos($award, '/') !== false) {
                $award = explode('/', $award);
            }

            $code = str_replace(['n/a', 'N/A'], '', @$fields['UCAS']) ?: null;

            $details = [
                'department' => $department,
                'level'      => $this->level,
                'title'      => $title,
                'code'       => $code,
                'award'      => $award,
                'entry_year' => $this->entryYear,
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

            if ($this->calendarModel) {
                $specification->calendar()->associate($this->calendarModel);
            }

            if ($award) {
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
            }

            if (!$specification->url) {
                try {
                    $url = $node->filter('a[href]')->first()->link()->getUri();

                    $specification->url = $url;
                    $specification->file = str_slug($title).'_'.$hash.'.html';
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
