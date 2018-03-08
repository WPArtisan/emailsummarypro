<?php
/**
 * Deals with ending summaries on a CRON hook.
 *
 * @package     email-summary-pro
 * @subpackage  Includes
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CRON hook for sending summaries.
 *
 * @param int $summary_id ID of the summary to do.
 * @return void
 */
function esp_send_summary( $summary_id = null ) {
	if ( ! $summary_id ) {
		return;
	}

	// Try and grab the summary.
	$summary = esp_get_summary( absint( $summary_id ) );

	if ( ! $summary ) {
		return;
	}

	// Setup the email.
	$email = new Email_Summary_Pro_Email( $summary );

	// Send the summary.
	$email->send();
}
add_action( 'esp_do_summary', 'esp_send_summary', 10, 1 );
