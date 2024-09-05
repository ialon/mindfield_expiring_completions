<?php

/**
 * Allows user to configure expiring completions for a course
 * Displays a list of Expiring Completions courses
 *
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/local/expiring_completions/lib.php');

admin_externalpage_setup('expiringcompletions');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('expiring_completions', 'local_expiring_completions'));

// Add expiring completions button
if (get_available_courses()) {
    $addexpirationurl = $CFG->wwwroot . '/local/expiring_completions/edit.php';
    $addexpirationhtml = html_writer::div(
        html_writer::link($addexpirationurl, get_string('addexpiration', 'local_expiring_completions'), ['class' => 'btn btn-secondary']),
        'addbutton text-right'
    );
    echo $OUTPUT->heading($addexpirationhtml);
}

/// Print the table of all configurations
$expirations = get_expiring_courses();

$table = new flexible_table('local_expiring_completions_administration_table');
$table->define_columns(array('courseid', 'name', 'enabled', 'expiration', 'threshold', 'actions'));
$table->define_headers(array(
    get_string('courseid', 'local_expiring_completions'),
    get_string('name', 'local_expiring_completions'),
    get_string('enabled', 'local_expiring_completions'),
    get_string('expiration', 'local_expiring_completions'),
    get_string('threshold', 'local_expiring_completions'),
    get_string('actions', 'local_expiring_completions')
));
$table->define_baseurl($PAGE->url);
$table->set_attribute('id', 'localexpiringcompletions'); 
$table->set_attribute('class', 'admintable generaltable');
$table->setup();

$stredit = get_string('edit');
$strdelete = get_string('delete');
$strdisable = get_string('disable', 'local_expiring_completions');
$strenable = get_string('enable', 'local_expiring_completions');

foreach ($expirations as $expiration) {
    $actions = '';

    // Course name with link
    $expirationname = html_writer::link(
        new moodle_url('/course/view.php', ['id' => $expiration->courseid]),
        $expiration->fullname
    );

    // Status
    $status = $expiration->enabled ? get_string('yes') : get_string('no');

    // Edit link
    $actions .= html_writer::link(
        new moodle_url('/local/expiring_completions/edit.php', ['id' => $expiration->id]),
        $OUTPUT->pix_icon('t/edit', $stredit),
        ['title' => $stredit]
    );

    // Enable/Disable link
    if ($expiration->enabled) {
        $actions .= html_writer::link(
            new moodle_url('/local/expiring_completions/edit.php', ['id' => $expiration->id, 'disable' => 1, 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/hide', $strdisable),
            ['title' => $strdisable]
        );
    } else {
        $actions .= html_writer::link(
            new moodle_url('/local/expiring_completions/edit.php', ['id' => $expiration->id, 'enable' => 1, 'sesskey' => sesskey()]),
            $OUTPUT->pix_icon('t/show', $strenable),
            ['title' => $strenable]
        );
    }

    // Delete link
    $actions .= html_writer::link(
        new moodle_url('/local/expiring_completions/edit.php', ['id' => $expiration->id, 'delete' => 1, 'sesskey' => sesskey()]),
        $OUTPUT->pix_icon('t/delete', $strdelete),
        ['title' => $strdelete]
    );

    $table->add_data(array($expiration->courseid, $expirationname, $status, $expiration->expiration, $expiration->threshold, $actions));
}

$table->print_html();

echo $OUTPUT->footer();
