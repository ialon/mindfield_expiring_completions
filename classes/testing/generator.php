<?php

 /**
 * This file contains the class that handles testing of emails
 *
 * @package    local_expiring_completions
 * @copyright  2024 Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions\testing;

defined('MOODLE_INTERNAL') || die();

class generator {
    /**
     * Creates a completion record for a user in a course.
     *
     * @param object $course The course object.
     * @param object $user The user object.
     * @param int $timecompleted The timestamp when the completion was marked as complete.
     * @return \completion_completion The completion object.
     */
    public static function create_completion($course, $user, $timecompleted) {
        // Complete the course for this user
        $ccompletion = new \completion_completion(array('course' => $course->id, 'userid' => $user->id));
        $ccompletion->mark_complete($timecompleted);

        return $ccompletion;
    }

    /**
     * Creates an expiration configuration for a course.
     *
     * @param object $course The course object.
     * @param int $expiration The expiration time in years.
     * @param int $threshold The threshold time in days.
     * @param string $subject The email subject (optional).
     * @param string $body The email body (optional).
     * @return object The created expiration configuration.
     */
    public static function create_expiration_configuration($course, $expiration, $threshold, $subject = '', $body = '') {
        global $DB;

        $data = new \stdClass();
        $data->courseid = $course->id;
        $data->enabled = 1;
        $data->expiration = $expiration;
        $data->threshold = $threshold;

        if (!empty($subject)) {
            $data->subject = $subject;
        } else {
            $data->subject = get_string('emailsubject:default', 'local_expiring_completions');
        }

        if (!empty($body)) {
            $data->body = $body;
        } else {
            $data->body = 'Unit testing email body';
        }

        $DB->insert_record('local_expiring_comp', $data);

        return $data;
    }
}
