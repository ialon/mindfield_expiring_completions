<?php

/**
 * Expiring completions
 *
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot.'/local/expiring_completions/lib.php');

/**
 * Class edit_form
*/
class edit_form extends moodleform {

    /**
     * Form definition
     */
    public function definition(): void {
        global $CFG;

        $mform =& $this->_form;

        // Header
        $mform->addElement('header','expiring_completions', get_string('expiring_completions', 'local_expiring_completions'));

        // Course
        $courses = get_available_courses();
        $mform->addElement('select', 'courseid', get_string('course'), $courses);
        $mform->setType('courseid', PARAM_INT);

        // Enable/disable processing
        $mform->addElement('selectyesno', 'enabled', get_string('enabled', 'local_expiring_completions'));
        $mform->addHelpButton('enabled', 'enabled', 'local_expiring_completions');
        $mform->setDefault('enabled', false);
        $mform->setType('enabled', PARAM_BOOL);

        // Completion validity period (in years)
        $options = array_combine(range(1,10), range(1,10));
        $mform->addElement('select', 'expiration', get_string('validityperiod', 'local_expiring_completions'), $options);
        $mform->addHelpButton('expiration', 'validityperiod', 'local_expiring_completions');
        $mform->setDefault('expiration', 5);
        $mform->setType('expiration', PARAM_INT);

        // Processing threshold (in days)
        $options = array_combine(range(1,100), range(1,100));
        $mform->addElement('select', 'threshold', get_string('processingthreshold', 'local_expiring_completions'), $options);
        $mform->addHelpButton('threshold', 'processingthreshold', 'local_expiring_completions');
        $mform->setDefault('threshold', 30);
        $mform->setType('threshold', PARAM_INT);

        // Email subject
        $mform->addElement('text', 'subject', get_string('emailsubject', 'local_expiring_completions'));
        $mform->addHelpButton('subject', 'emailbody', 'local_expiring_completions');
        $mform->addRule('subject', get_string('missingsubject', 'local_expiring_completions'), 'required', null, 'client');
        $mform->setType('subject', PARAM_TEXT);
        $mform->setDefault('subject', get_string('emailsubject:default', 'local_expiring_completions'));

        // Email body
        $editoroptions = $this->_customdata['editoroptions'];
        $mform->addElement('editor', 'body_editor', get_string('emailbody', 'local_expiring_completions'), null, $editoroptions);
        $mform->addHelpButton('body_editor', 'emailbody', 'local_expiring_completions');
        $mform->addRule('body_editor', get_string('missingbody', 'local_expiring_completions'), 'required', null, 'client');
        $mform->setType('body_editor', PARAM_RAW);

        if (isset($this->_customdata['expiration']->id) && $this->_customdata['expiration']->id) {
            $mform->addElement('hidden', 'id', $this->_customdata['expiration']->id);
            $mform->setType('id', PARAM_INT);
        }

        // Buttons.
        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
