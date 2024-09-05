<?php
/**
 * Settings for Expiring Completions
 *
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $page = new admin_externalpage(
        'expiringcompletions',
        new lang_string('expiring_completions', 'local_expiring_completions'),
        $CFG->wwwroot . '/local/expiring_completions/manage.php'
    );
    $ADMIN->add('courses', $page);
}
