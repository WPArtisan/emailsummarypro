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

/**
 * Show a summary preview in browser.
 *
 * @return void
 */
function esp_preview_summary() {
	if ( ! isset( $_GET['summary_id'] ) ) {
		return;
	}

	// Check the nonce.
	if ( ! isset( $_GET['esp_nonce'] ) || ! wp_verify_nonce( $_GET['esp_nonce'], 'preview_summary' ) ) {
		return;
	}

	// Grab the summary ID.
	$summary_id = absint( $_GET['summary_id'] );

	// Try and grab the summary.
	$summary = esp_get_summary( $summary_id );

	if ( ! $summary ){
		return;
	}

	// if a custom date is set, use that instead.
	if ( ! empty( $_GET['date'] ) ) {
		$summary->set_date( sanitize_text_field( wp_unslash( $_GET['date'] ) ) );
	}

	// Show the preview.
	echo esp_get_template( $summary );
	die;
}
add_action( 'esp_preview_summary', 'esp_preview_summary', 10 );
