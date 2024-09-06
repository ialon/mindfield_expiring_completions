<?php

/**
 * This plugin sends users emails based on expiring course completions
 *
 * @package    local_expiring_completions
 * @copyright  2024 Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions\helper;

class completion {
    /**
     * Retrieves a list of expiring completions.
     *
     * This function queries the database to retrieve a list of users with expiring completions.
     * It checks if the completion is enabled and if the associated course exists.
     * For each expiration, it calculates the expiration time based on the expiration period and threshold.
     * Then, it retrieves the course completions that have a completion time earlier than the calculated expiration time.
     *
     * @return array An array of expiring completions, each containing the expiration details and associated course completions.
     */
    public static function get_expiring_completions() {
        // Get the list of users with expiring completions
        global $DB;

        $sql = "SELECT ec.*
                  FROM {local_expiring_comp} ec
             LEFT JOIN {course} c ON ec.courseid = c.id
                 WHERE c.id IS NOT NULL AND ec.enabled = 1";
                 
        $expirations = $DB->get_records_sql($sql);

        foreach ($expirations as $expiration) {
            $expiretime = time() - $expiration->expiration * YEARSECS + $expiration->threshold * DAYSECS;

            $sql = "SELECT cc.*
                      FROM {course_completions} cc
                     WHERE cc.timecompleted < :expiretime
                       AND cc.course = :courseid";
            $params = ['expiretime' => $expiretime, 'courseid' => $expiration->courseid];

            $expiration->completions = $DB->get_records_sql($sql, $params);
        }

        return $expirations;
    }

    /**
     * Expire enrolments for a completion.
     *
     * This function is responsible for expiring enrolments for a given completion.
     * It loops through all enrolment instances associated with the course of the completion,
     * checks if the user is enrolled in each instance, and if so, unenrols the user.
     *
     * @param object $completion The completion object.
     * @return void
     */
    public static function expire_enrolments($completion) {
        global $DB;

        $instances = $DB->get_records('enrol', array('courseid' => $completion->course));

        foreach ($instances as $instance) {
            if (!$ue = $DB->get_record('user_enrolments', array('enrolid' => $instance->id, 'userid' => $completion->userid))) {
                continue;
            }

            if (!enrol_is_enabled($instance->enrol)) {
                continue;
            }

            if (!$plugin = enrol_get_plugin($instance->enrol)) {
                continue;
            }

            if (!$plugin->allow_unenrol_user($instance, $ue)) {
                continue;
            }

            $plugin->unenrol_user($instance, $ue->userid);
        }
    }
}
