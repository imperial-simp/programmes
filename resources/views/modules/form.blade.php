@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-heading">Module Details</div>
                        <div class="panel-body">
                            <!-- moduleDetails -->
                            <fieldset>
                                <legend>Details</legend>
                                <!-- ossCode -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="oss_code">Current Code</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="oss_code" name="oss_code">
                                    </div>
                                </div>
                                <!-- /ossCode -->
                                <!-- ossTitle -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="oss_title">Long Title</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="oss_title" name="oss_title">
                                    </div>
                                </div>
                                <!-- /ossTitle -->
                                <!-- description -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="description">Brief Description</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="description" name="description"></textarea>
                                    </div>
                                </div>
                                <!-- /description -->
                                <!-- bannerCode -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="banner_code">Banner Code</label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="banner_code" name="banner_code">
                                    </div>
                                </div>
                                <!-- /bannerCode -->
                                <!-- bannerTitle -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="banner_title">Banner Title</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="banner_title" name="banner_title" maxlength="30">
                                    </div>
                                </div>
                                <!-- /bannerTitle -->
                                <!-- creditValue -->
                                {{-- <div class="row"> --}}
                                    <!-- ects -->
                                    {{-- <div class="col-md-6"> --}}
                                        <div class="form-group">
                                            <label class="control-label col-md-3" for="ects">ECTS</label>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" id="ects" name="ects" min="0" max="1000">
                                            </div>
                                        {{-- </div> --}}
                                    {{-- </div> --}}
                                    <!-- /ects -->
                                    <!-- cats -->
                                    {{-- <div class="col-md-6"> --}}
                                        {{-- <div class="form-group"> --}}
                                            <label class="control-label col-md-1" for="cats">CATS</label>
                                            <div class="col-md-3">
                                                <input type="number" class="form-control" id="cats" name="cats" min="0" max="1000">
                                            </div>
                                        </div>
                                    {{-- </div> --}}
                                    <!-- /cats -->
                                {{-- </div> --}}
                                <!-- /creditValue -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="fheq">FHEQ Level</label>
                                    <div class="col-md-3">
                                        <select class="form-control" id="fheq" name="fheq">
                                            <option></option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="0">0 (Foundation)</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                            <!-- /moduleDetails -->

                            <!-- moduleDelivery -->
                            <fieldset>
                                <legend>Delivery</legend>
                                <!-- moduleDeliveryOptions -->
                                <div class="form-group">
                                    <!-- deliveryMode -->
                                    <div class="col-md-4">
                                        <label class="control-label" for="delivery_mode">Mode of Delivery</label>
                                        @foreach([
                                            'taught' => 'Taught',
                                            'distance' => 'Distance',
                                            'placement' => 'Placement',
                                            'online' => 'Online',
                                            ] as $value => $option)
                                            <div class="checkbox">
                                                <label><input type="checkbox" value="{{ $value }}" name="delivery_mode[]">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        <div class="checkbox">
                                            <input type="text" class="form-control input-sm" placeholder="Other" name="delivery_mode[]">
                                        </div>
                                    </div>
                                    <!-- deliveryMode -->
                                    <!-- /deliveryPattern -->
                                    <div class="col-md-4">
                                        <label class="control-label" for="delivery_pattern">Pattern of Delivery</label>
                                        @foreach([
                                            'week' => 'Weekly',
                                            'block' => 'Block',
                                            ] as $value => $option)
                                            <div class="checkbox">
                                                <label><input type="checkbox" value="{{ $value }}" name="delivery_pattern[]">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        <div class="checkbox">
                                            <input type="text" class="form-control input-sm" placeholder="Other" name="delivery_pattern[]">
                                        </div>
                                    </div>
                                    <!-- /deliveryPattern -->
                                    <!-- /deliveryTiming -->
                                    <div class="col-md-4">
                                        <label class="control-label" for="delivery_timing">Timing of Delivery</label>
                                        @foreach([
                                            'autumn' => 'Autumn Term',
                                            'spring' => 'Spring Term',
                                            'summer' => 'Summer Term',
                                            'year' => 'Whole Year',
                                            ] as $value => $option)
                                            <div class="checkbox">
                                                <label><input type="checkbox" value="{{ $value }}" name="delivery_timing[]">{{ $option }}</label>
                                            </div>
                                        @endforeach
                                        <div class="checkbox">
                                            <input type="text" class="form-control input-sm" placeholder="Other" name="delivery_timing[]">
                                        </div>
                                    </div>
                                    <!-- /deliveryTiming -->
                                </div>
                                <!-- /moduleDeliveryOptions -->
                            </fieldset>
                            <!-- /moduleDelivery -->

                            <!-- ownership -->
                            <fieldset>
                                <legend>Ownership</legend>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="owning_department">Owning Department</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="owning_department" name="owning_department">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="campus">Delivery Campus</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="campus" name="campus">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Associated Staff</legend>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-md-2">CID</th>
                                            <th class="col-md-6">Full Name</th>
                                            <th class="col-md-3">Association</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ([1,2,3] as $i)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control input-sm" name="staff[]">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control input-sm" name="staff[]">
                                                </td>
                                                <td>
                                                    <select class="form-control input-sm" name="staff[]">
                                                        <option></option>
                                                        @foreach([
                                                            'module_leader' => 'Module leader',
                                                            'topic_leader' => 'Topic leader',
                                                            'coordinator' => 'Coordinator',
                                                            ] as $value => $option)
                                                            <option value="{{ $value }}">{{ $option }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </fieldset>
                            <!-- /ownership -->
                            <!-- relatedModules -->
                            <fieldset>
                                <legend>Associated Modules</legend>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-md-2">Code</th>
                                            <th class="col-md-7">Module Title</th>
                                            <th class="col-md-3">Requisite Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ([1,2,3] as $i)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control input-sm" name="modules[code][]">
                                                </td>
                                                <td>
                                                    <p class="form-control-static input-sm">Module not found.</p>
                                                </td>
                                                <td>
                                                    <select class="form-control input-sm" name="modules[type][]">
                                                        <option></option>
                                                        @foreach([
                                                            'prereq' => 'Prerequisite',
                                                            'coreq' => 'Corequisite',
                                                            'antireq' => 'Antirequisite',
                                                            ] as $value => $option)
                                                            <option value="{{ $value }}">{{ $option }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </fieldset>
                            <!-- /relatedModules -->
                            <!-- programmes -->
                            <fieldset>
                                <legend>Associated Programmes</legend>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="col-md-2">Code</th>
                                            <th class="col-md-4">Programme Title</th>
                                            <th class="col-md-2">Year(s)</th>
                                            <th class="col-md-1">Core</th>
                                            <th class="col-md-3">Elective Group</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ([1,2,3] as $i)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control input-sm" name="programmes[code][]">
                                                </td>
                                                <td>
                                                    <p class="form-control-static input-sm">Programme not found.</p>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control input-sm" name="programmes[year][]">
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="programmes[core][]" value="1">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control input-sm" name="programmes[elective_group][]">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </fieldset>
                            <!-- /programmes -->
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Learning and Teaching Details</div>
                        <div class="panel-body">
                            <!-- moduleContent -->
                            <fieldset>
                                <legend>Module Content</legend>
                                <!-- aims -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="aims">Aims</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="aims" name="aims" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- /aims -->
                                <!-- outcomes -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="outcomes">Learning Outcomes</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="outcomes" name="outcomes" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- /outcomes -->
                                <!-- content -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="content">Content</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="content" name="content" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- /content -->
                                <!-- support -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="support">Learning Support</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="support" name="support" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- /support -->
                                <!-- activities -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="activities">Learning and Teaching Activities</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="activities" name="activities" rows="3"></textarea>
                                    </div>
                                </div>
                                <!-- /activities -->
                            </fieldset>
                            <!-- /moduleContent -->
                            <!-- studyHours -->
                            <fieldset>
                                <legend>Allocation of Study Hours <small class="text-muted">(indicative)</small></legend>
                                <!-- teachingHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="teaching_hours">Scheduled Hours</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="teaching_hours" name="teaching_hours">
                                    </div>
                                </div>
                                <div class="form-group show-help">
                                    <div class="col-md-offset-3 col-md-8">
                                        <span class="help-block">This is an indication of the number of hours students can expect to spend in scheduled teaching activities including lectures, seminars, tutorials, project supervision, demonstrations, practical classes and workshops, supervised time in workshops/ studios, fieldwork, and external visits.</span>
                                    </div>
                                </div>
                                <!-- /teachingHours -->
                                <!-- studyHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="study_hours">Independent Study</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="study_hours" name="study_hours">
                                    </div>
                                </div>
                                <div class="form-group show-help">
                                    <div class="col-md-offset-3 col-md-8">
                                        <span class="help-block">All students are expected to undertake guided independent study which includes wider reading/ practice, follow-up work, the completion of assessment tasks, and revisions.</span>
                                    </div>
                                </div>
                                <!-- /studyHours -->
                                <!-- placementHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="placement_hours">Placement</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="placement_hours" name="placement_hours">
                                    </div>
                                </div>
                                <div class="form-group show-help">
                                    <div class="col-md-offset-3 col-md-8">
                                        <span class="help-block">The placement is a specific type of learning away from the College. It includes work-based learning and study that occurs overseas.</span>
                                    </div>
                                </div>
                                <!-- /placementHours -->
                                <!-- totalHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3">Total Hours</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" disabled>
                                    </div>
                                </div>
                                <!-- /totalHours -->
                            </fieldset>
                            <!-- /studyHours -->
                            <!-- assessments -->
                            <fieldset>
                                <legend>Assessment Weighting</legend>
                                <!-- teachingHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="assessment_written">Written Examination</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="assessment_examination" name="assessment_examination">
                                    </div>
                                </div>
                                <div class="form-group show-help">
                                    <div class="col-md-offset-3 col-md-8">
                                        <span class="help-block">Written examination.</span>
                                    </div>
                                </div>
                                <!-- /teachingHours -->
                                <!-- studyHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="assessment_coursework">Coursework</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="assessment_coursework" name="assessment_coursework">
                                    </div>
                                </div>
                                <div class="form-group show-help">
                                    <div class="col-md-offset-3 col-md-8">
                                        <span class="help-block">Written assignment/ essay, report, dissertation, portfolio, project output, set exercise.</span>
                                    </div>
                                </div>
                                <!-- /studyHours -->
                                <!-- placementHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="assessment_practical">Practical</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" id="assessment_practical" name="assessment_practical">
                                    </div>
                                </div>
                                <div class="form-group show-help">
                                    <div class="col-md-offset-3 col-md-8">
                                        <span class="help-block">Oral assessment and presentation, practical skills assessment, set exercise.</span>
                                    </div>
                                </div>
                                <!-- /placementHours -->
                                <!-- totalHours -->
                                <div class="form-group">
                                    <label class="control-label col-md-3">Total Weighting</label>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" disabled>
                                    </div>
                                </div>
                                <!-- /totalHours -->
                            </fieldset>
                            <!-- /assessments -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
