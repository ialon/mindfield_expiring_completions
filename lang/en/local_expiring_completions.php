<?php
/**
 * Plugin for resetting expiring completions and notifying users
 *
 * @package    local_expiring_completions
 * @copyright  Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = "Expiring Completions";
$string['checkcompletions'] = 'Check and process completions about to expire';

// Settings
$string['expiring_completions'] = 'Expiring Completions';

// Management table
$string['addexpiration'] = 'Configure expiration';
$string['courseid'] = 'Course ID';
$string['name'] = 'Course name';
$string['enabled'] = 'Enabled';
$string['expiration'] = 'Validity (years)';
$string['threshold'] = 'Threshold (days)';
$string['actions'] = 'Actions';
$string['enable'] = "Enable";
$string['disable'] = "Disable";

// Edit page
$string['newexpiration'] = 'New expiration';
$string['expirationdoesnotexist'] = 'Course expiration settings do not exist';
$string['coursedoesnotexist'] = 'Course does not exist';
$string['cannotupdateexpiration'] = 'Error updating expiration';
$string['confirmdeleteexpiration'] = 'Are you sure you want to delete this configuration?';
$string['cannotdeleteexpiration'] = 'Error deleting expiration';
$string['errorsavingexpiration'] = 'Error saving expiration';
$string['expiration_saved'] = 'Expiration saved';

// Form
$string['enabled_help'] = 'If enabled, the system will check for completions that are about to expire and notify users.';
$string['validityperiod'] = 'Completion validity period (years)';
$string['validityperiod_help'] = 'The number of years a completion is valid for.';
$string['processingthreshold'] = 'Process before expiration (days)';
$string['processingthreshold_help'] = 'The number of days before a completion expires that the system will start notifying users.';
$string['emailsubject'] = 'Email subject';
$string['missingsubject'] = 'Subject is required';
$string['emailsubject:default'] = 'Your completion is about to expire';
$string['emailbody'] = 'Email body';
$string['missingbody'] = 'Body is required';