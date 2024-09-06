<?php

/**
 * This plugin sends users emails based on expiring course completions
 *
 * @package    local_expiring_completions
 * @copyright  2024 Josemaria Bolanos <josemabol@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_expiring_completions\helper;

use \local_expiring_completions\helper\log;

class email {
    /**
     * Sends an expiring completion email to a user.
     *
     * @param string $subject The subject of the email.
     * @param string $body The body of the email.
     * @param object $completion The completion object.
     * @return void
     */
    public static function send_expiring_completion_email($subject, $body, $completion) {
        global $CFG;

        $context = \context_system::instance();
        $sender = \core_user::get_noreply_user();
        $user = \core_user::get_user($completion->userid);

        $body = file_rewrite_pluginfile_urls($body, 'pluginfile.php', $context->id, 'local_engagement_email', 'body', 0);

        $options = (object) [
            'overflowdiv' => true,
            'noclean' => true,
            'para' => false,
            'context' => $context
        ];
        $body = format_text($body, FORMAT_HTML, $options);

        $result = email_to_user(
            $user,
            $sender,
            $subject,
            html_to_text($body),
            $body
        );

        if ($result) {
            log::create_record($subject, $body, $completion);
        }
    }
}
