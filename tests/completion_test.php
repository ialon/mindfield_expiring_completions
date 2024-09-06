<?php

 /**
 * This file contains the class that handles testing of emails
 *
 * @package    local_expiring_completions
 * @copyright  2024 Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions;

use local_expiring_completions\testing\generator;
use local_expiring_completions\task\check_completions;

class completion_test extends \advanced_testcase {
    /**
     * Tests set up
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_expire_enrolments() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $user = $this->getDataGenerator()->create_user();

        // Create an expiration configuration.
        $subject = 'Your certificate is about to expire!';
        $body = 'Your completion has been reset. Please complete the course again!';
        $expiration = generator::create_expiration_configuration($course, 1, 30, $subject, $body);

        // Enrol the user in the course.
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Create an expired completion.
        $completion = generator::create_completion($course, $user, time() - YEARSECS);

        // Execute scheduled task
        $task = new check_completions();
        $task->execute();

        // Check that the user has been unenrolled.
        $enrols = $DB->get_records('enrol', ['courseid' => $course->id]);
        foreach ($enrols as $enrol) {
            $this->assertFalse($DB->record_exists('user_enrolments', ['userid' => $user->id, 'enrolid' => $enrol->id]));
        }

        // Check the log record.
        $logs = $DB->get_records('local_expiring_comp_log');
        $this->assertSame(1, count($logs));
    }
}
