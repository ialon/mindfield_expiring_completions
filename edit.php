<?php

/**
 * Expiring completions
 *
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');

$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$enable = optional_param('enable', 0, PARAM_BOOL);
$disable = optional_param('disable', 0, PARAM_BOOL);

require_login();
$context = context_system::instance();

$PAGE->set_context($context);

$coursesettingsurl = new moodle_url($CFG->wwwroot . '/admin/search.php?#linkcourses');
$PAGE->navbar->add(get_string('courses', 'admin'), $coursesettingsurl);

$manageurl = new moodle_url($CFG->wwwroot . '/local/expiring_completions/manage.php');
$PAGE->navbar->add(get_string('expiring_completions', 'local_expiring_completions'), $manageurl);

if ($id) {
    if (!$expiration = $DB->get_record('local_expiring_comp', ['id' => $id])) {
        throw new moodle_exception('expirationdoesnotexist', 'local_expiring_completions');
    }

    if (!$course = $DB->get_record('course', ['id' => $expiration->courseid])) {
        throw new moodle_exception('coursedoesnotexist', 'local_expiring_completions');
    }

    $title = format_string($course->fullname);

    $PAGE->set_url('/local/expiring_completions/edit.php', ['id' => $id]);
} else {
    $expiration = new stdClass();
    $title = get_string('newexpiration', 'local_expiring_completions');
    $PAGE->set_url('/local/expiring_completions/edit.php', null);
}

$PAGE->navbar->add($title);

// Common actions.
if (($enable || $disable) && confirm_sesskey()) {
    $enabled = ($enable) ? 1 : 0;
    if (!$DB->set_field('local_expiring_comp', 'enabled', $enabled, ['id' => $expiration->id])) {
        throw new moodle_exception('cannotupdateexpiration', 'local_expiring_completions');
    }

    header("Location: $CFG->wwwroot/local/expiring_completions/manage.php");
    die;
}

if ($delete && confirm_sesskey()) {
    if (!$confirm) {
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        echo $OUTPUT->header();
        $message = get_string('confirmdeleteexpiration', 'local_expiring_completions');
        $optionsyes = ['id' => $expiration->id, 'delete' => $delete, 'sesskey' => sesskey(), 'confirm' => 1];
        $optionsno = [];
        $buttoncontinue = new single_button(new moodle_url('edit.php', $optionsyes), get_string('yes'), 'get');
        $buttoncancel = new single_button(new moodle_url('manage.php', $optionsno), get_string('no'), 'get');
        echo $OUTPUT->confirm($message, $buttoncontinue, $buttoncancel);
        echo $OUTPUT->footer();
        exit;
    }

    $DB->delete_records('local_expiring_comp', ['id' => $expiration->id]);
    header("Location: $CFG->wwwroot/local/expiring_completions/manage.php");
    die;
}

require_once('edit_form.php');

// Prepare editor
$editoroptions = array(
    'maxbytes' => $CFG->maxbytes,
    'maxfiles' => EDITOR_UNLIMITED_FILES,
    'changeformat' => 0,
    'context' => $context,
    'noclean' => true,
    'trusttext' => false
);

$expiration->bodyformat = FORMAT_HTML;
if (!empty($expiration->id)) {
    $expiration = file_prepare_standard_editor($expiration, 'body', $editoroptions, $context, 'local_engagement_email', 'body', 0);
} else {
    $expiration = file_prepare_standard_editor($expiration, 'body', $editoroptions, $context, 'local_engagement_email', 'body', null);
}

if (!empty($expiration)) {
    $editform = new edit_form('edit.php', compact('editoroptions', 'expiration'));
} else {
    $editform = new edit_form('edit.php', compact('editoroptions'));
}

if (!empty($expiration)) {
    $editform->set_data($expiration);
}

if ($editform->is_cancelled()) {
    redirect($CFG->wwwroot . '/local/expiring_completions/manage.php');
} else if ($data = $editform->get_data()) {
    // Save the files used in the body editor and store
    $data = file_postupdate_standard_editor($data, 'body', $editoroptions, $context, 'local_expiring_completions', 'body', 0);

    if (empty($data->id)) {
        if (!$lastid = $DB->insert_record('local_expiring_comp', $data)) {
            throw new moodle_exception('errorsavingexpiration', 'local_expiring_completions');
        }
    } else {
        if (!$DB->update_record('local_expiring_comp', $data)) {
            throw new moodle_exception('errorsavingexpiration', 'local_expiring_completions');
        }
    }

    \core\notification::add(
        get_string('expiration_saved', 'local_expiring_completions'),
        \core\notification::SUCCESS
    );

    redirect($CFG->wwwroot . '/local/expiring_completions/manage.php');
}

$PAGE->set_heading($title);

echo $OUTPUT->header();

$editform->display();

echo $OUTPUT->footer();
