<?php

/**
 * Task definition for Expiring Completions
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions\task;

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
        // Todo: Implement the task
    }
}
