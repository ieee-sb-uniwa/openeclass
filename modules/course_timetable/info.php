<?php

/* ========================================================================
 * Open eClass 3.0
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2014  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */

/**
 *  @file info.php
 *  @brief edit course unit
 */

$require_current_course = true;
$require_editor = true;
$require_help = true;
$helpTopic = 'AddCourseUnits';
require_once '../../include/baseTheme.php';
require_once 'modules/tags/moduleElement.class.php';
$pageName = $langEditUnit;

load_js('tools.js');
load_js('select2');
load_js('bootstrap-timepicker');

$head_content .= <<<hContent
    <script type="text/javascript">
    /* <![CDATA[ */

        $(document).ready(function() {
            $('#start_hour').timepicker({
                showMeridian: false,
                format: 'hh:ii',
                pickerPosition: 'bottom-right',
                minuteStep: 10,
                defaultTime: false,
                autoclose: true
            });
            $('#end_hour').timepicker({
                showMeridian: false,
                format: 'hh:ii',
                pickerPosition: 'bottom-right',
                minuteStep: 10,
                defaultTime: false,
                autoclose: true
            });

        });

    /* ]]> */
    </script>
hContent;

$start_week = $finish_week = '';
if (isset($_GET['edit'])) { // display form for editing course unit
    // $tool_content .= '<div> hello </div>';
    // $id = $_GET['edit'];
    // $cu = Database::get()->querySingle("SELECT id, title, comments, start_week, finish_week FROM course_units WHERE id = ?d AND course_id = ?d",$id, $course_id);
    // if (!$cu) {
    //     Session::Messages($langUnknownResType);
    //     redirect_to_home_page("courses/$course_code/");
    // }
    // $unittitle = " value='" . htmlspecialchars($cu->title, ENT_QUOTES) . "'";
    // $tagsInput = eClassTag::tagInput($id);
    // $unitdescr = $cu->comments;
    // if (!(is_null($cu->start_week))) {
    //     $start_week = DateTime::createFromFormat('Y-m-d', $cu->start_week)->format('d-m-Y');
    // }
    // if (!(is_null($cu->finish_week))) {
    //     $finish_week = DateTime::createFromFormat('Y-m-d', $cu->finish_week)->format('d-m-Y');
    // }
    // $unit_id = $cu->id;
} else {
    $pageName = 'Add timetable';
    $tagsInput = eClassTag::tagInput();
    $unitdescr = $unittitle = '';
}

$actionAppend = isset($unit_id) ? "&amp;id=$unit_id" : "";

$tool_content .= action_bar(array(
        array('title' => $langBack,
            'url' => "{$urlServer}courses/$course_code",
            'icon' => 'fa-reply',
            'level' => 'primary-label')),false);

// get course_id, userid
// start hour, end hour, day of week, repeat

$room = "Room";

// <form class='form-horizontal' role='form' method='post' action='index.php?course=$course_code$actionAppend' onsubmit=\"return checkrequired(this, 'unittitle');\">

$tool_content .= "<div class='form-wrapper'>
        <form class='form-horizontal' role='form' method='post' action='index.php?course=$course_code'>
            <div class='form-group'>
                <label for='day_of_week' class='col-sm-2 control-label'>$langDay:</label>
                <div class='col-sm-10'>
                <select class='form-control' name='day_of_week' id='day_of_week'>
                    <option value='1'>". q($langDay_of_weekNames['long'][1]) ."</option>
                    <option value='2'>". q($langDay_of_weekNames['long'][2]) ."</option>
                    <option value='3'>". q($langDay_of_weekNames['long'][3]) ."</option>
                    <option value='4'>". q($langDay_of_weekNames['long'][4]) ."</option>
                    <option value='5'>". q($langDay_of_weekNames['long'][5]) ."</option>
                    <option value='6'>". q($langDay_of_weekNames['long'][6]) ."</option>
                    <option value='0'>". q($langDay_of_weekNames['long'][0]) ."</option>
                </select>
                </div>
            </div>
            <div class='input-append bootstrap-timepicker form-group'>
                <label for='start_hour' class='col-sm-2 control-label'>$langStart:</label>
                <div class='col-sm-10'>
                    <div class='input-group add-on'>
                        <input class='form-control input-small' name='start_hour' id='start_hour' type='text' value='8:00'>
                        <div class='input-group-addon'><span class='fa fa-clock-o fa-fw'></span></div>
                    </div>
                </div>
            </div>
            <div class='input-append bootstrap-timepicker form-group'>
                <label for='end_hour' class='col-sm-2 control-label'>$langFinish:</label>
                <div class='col-sm-10'>
                    <div class='input-group add-on'>
                        <input class='form-control input-small' name='end_hour' id='end_hour' type='text' value='9:00'>
                        <div class='input-group-addon'><span class='fa fa-clock-o fa-fw'></span></div>
                    </div>
                </div>
            </div>
            <div class='form-group'>
                <label for='room' class='col-sm-2 control-label'>$room:</label>
                <div class='col-sm-10'>
                  <input name='room' id='room' type='text' class='form-control'>
                </div>
            </div>
            <div class='form-group'>
                <div class='col-sm-10 col-sm-offset-2'>
                    <input class='btn btn-primary' type='submit' name='edit_submit' value='" . q($langSubmit) . "'>
                </div>
            </div>
        </form>
    </div>";

draw($tool_content, 2, null, $head_content);
