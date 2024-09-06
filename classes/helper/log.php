<?php

/**
 * This plugin sends users emails based on expiring course completions
 *
 * @package    local_expiring_completions
 * @copyright  2024 Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions\helper;

class log {
    /**
     * Creates a log record for expiring completions.
     *
     * @param string $subject The subject of the log record.
     * @param string $body The body of the log record.
     * @param object $completion The completion object.
     * @return void
     */
    public static function create_record($subject, $body, $completion) {
        global $DB;

        $log = new \stdClass();
        $log->courseid = $completion->course;
        $log->userid = $completion->userid;
        $log->subject = $subject;
        $log->body = $body;
        $log->timecreated = time();
        
        $DB->insert_record('local_expiring_comp_log', $log);
    }
}
