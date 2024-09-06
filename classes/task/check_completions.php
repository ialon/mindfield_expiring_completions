<?php

/**
 * Task definition for Expiring Completions
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions\task;

use \local_expiring_completions\helper\completion as completion_helper;
use \local_expiring_completions\helper\email as email_helper;

defined('MOODLE_INTERNAL') || die();

class check_completions extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('checkcompletions', 'local_scheduled_reports');
    }

    /**
     * Prepare and send emails
     */
    public function execute() {
        // Get the list of users with expiring completions
        $expirations = completion_helper::get_expiring_completions();

        foreach ($expirations as $expiration) {
            foreach ($expiration->completions as $completion) {
                // Reset all activities

                // Expire enrolments for the users
                completion_helper::expire_enrolments($completion);

                // Delete customcerts

                // Notify the user
                email_helper::send_expiring_completion_email($expiration->subject, $expiration->body, $completion);
            }
        }
    }
}
