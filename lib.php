<?php

/**
 * This file contains functions used by the scheduled reports plugin
 *
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Retrieves a list of expiring courses.
 *
 * This function queries the database to retrieve a list of expiring courses along with their details.
 *
 * @global moodle_database $DB
 * @return array An array of expiring courses, each containing the course ID, full name, and additional details.
 */
function get_expiring_courses() {
    global $DB;

    $sql = "SELECT c.id AS courseid, c.fullname, ec.*
              FROM {local_expiring_comp} ec
         LEFT JOIN {course} c ON c.id = ec.courseid
             WHERE c.id IS NOT NULL
          ORDER BY c.id";

    $courses = $DB->get_records_sql($sql);

    return $courses;
}


function get_available_courses() {
    global $DB;

    $concat = $DB->sql_concat("'(ID: '", 'c.id', "') '", 'c.fullname');

    $sql = "SELECT c.id, " . $concat . " AS coursename
              FROM {course} c
         LEFT JOIN {local_expiring_comp} ec ON c.id = ec.courseid
             WHERE ec.id IS NULL AND c.id > 1
          ORDER BY c.id";

    $courses = $DB->get_records_sql_menu($sql);

    return $courses;
}
