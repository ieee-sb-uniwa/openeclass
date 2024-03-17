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
require_once '../../include/baseTheme.php';

if(isset($_POST['edit_submit'])) {

    $day_of_week = $_POST['day_of_week'];
    $start_hour = $_POST['start_hour'];
    $end_hour = $_POST['end_hour'];
    $room = $_POST['room'];

    Database::get()->query("INSERT INTO courses_timetable(created_by,course_id,day_of_week,start_hour,end_hour,room) 
                                values (?d,?d,?d,?s,?s,?s)", $uid, $course_id, $day_of_week, $start_hour, $end_hour, $room);   
}

header("Location: {$urlServer}courses/$course_code");