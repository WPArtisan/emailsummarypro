<?php
/**
 * Sets up all the CRON hooks required for sending the summaries.
 *
 * @package     email-summary-pro
 * @subpackage  Includes
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Setup the CRON jobs for all active summaries.
 *
 * @return void
 */
function esp_setup_cron_jobs() {

	// Send any summaries due in the next hour.
	if ( ! wp_next_scheduled( 'esp_send_summaries' ) ) {
		wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'esp_send_summaries' );
	}

}
add_action( 'wp', 'esp_setup_cron_jobs', 10 );

/**
 * Check for any summaries due to go out in the next hour and send them.
 *
 * @return void.
 */
function esp_check_for_summaries_to_send() {
	// Find summaries due in the next hour.
	$args = array(
		'posts_per_page' => 9999,
		'post_status'    => array( 'active' ),
	);

	$summaries = esp_get_summaries( $args );

	if ( ! $summaries ) {
		return;
	}

	foreach ( $summaries as $summary ) {
		// Setup the email.
		$email = new ESP_Email( $summary );
		// Send the summary.
		$email->send();
	}
}
add_action( 'esp_send_summaries', 'esp_check_for_summaries_to_send', 10, 0 );
