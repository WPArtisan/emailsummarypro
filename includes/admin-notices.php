<?php
/**
 * Admin Notices.
 *
 * @package     email-summary-pro
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Admin Notices.
 *
 * A better way of handling admin messages globally.
 *
 * @return void
 */
function esp_admin_notices() {

	// No message set, bail early.
	if ( empty( $_GET['esp-notice'] ) ) { // Input var okay.
		return;
	}

	switch ( $_GET['esp-notice'] ) { // Input var okay.

		// Placements.
		case 'resend_summary_success':
			add_settings_error( 'esp-notices', 'esp-resend-summary-succes', esc_html__( 'Email Summary successfully resent. Please check your Email.', 'email-summary-pro' ), 'updated' );
			break;

		default:

			/**
			 * Use this action to add any custom notices.
			 *
			 * @since 1.4.0
			 */
			do_action( 'esp_notices' );

			break;
	}

	// Check for any errors set in the transient.
	// These are generally errors that aren't set by the plugin.
	// e.g. ones returned from Facebook.
	if ( $notices = get_transient( 'esp_notices' ) ) {

		foreach ( $notices as $notice ) {
			add_settings_error( 'esp-notices', $notice['code'], esc_html( $notice['message'] ), $notice['type'] );
		}

		// Clear the transient.
		delete_transient( 'esp_notices' );
	}

}
add_action( 'admin_notices', 'esp_admin_notices', 10, 0 );
add_action( 'network_admin_notices', 'esp_admin_notices', 10, 0 );

/**
 * Dismiss any notices that may be shown if the dismiss link is clicked..
 *
 * @since 1.3.5
 * @return void
 */
function esp_dismiss_notices() {

	// No message set, bail early.
	if ( empty( $_GET['esp-notice'] ) ) { // Input var okay.
		return;
	}

	// Check the nonce.
	// @codingStandardsIgnoreLine
	if ( ! isset( $_GET['esp-dismiss-notice-nonce'] ) || ! wp_verify_nonce( $_GET['esp-dismiss-notice-nonce'], 'esp-dismiss-notice' ) ) {
		wp_die( 'Invalid nonce - Email Summary Pro Dismiss Notice Action' );
	}

	switch ( $_GET['esp-notice'] ) { // Input var okay.

		// They've already rated the app, kill all rating prompts.
		case 'rating_permanent':
			delete_site_option( 'esp_rating_prompts' );
		break;

		// They don't want to be bugged anymore at the moment.
		// Remove the current interval prompt.
		case 'rating_temporary':

			$prompts = (array) get_site_option( 'esp_rating_prompts' );

			// 1 or fewer intervals and just remove the whole option.
			if ( count( $prompts ) <= 1 ) {
				delete_site_option( 'esp_rating_prompts' );

			} else {
				// Sort the array and remove the lowest interval.
				sort( $prompts, SORT_NUMERIC );
				array_shift( $prompts );
				update_site_option( 'esp_rating_prompts', $prompts );
			}

		break;

		default;

			/**
			 * Use this action to dismiss any custom notices.
			 *
			 * @since 1.4.0
			 */
			do_action( 'esp_dismiss_notices_default' );

			break;

	}

	wp_safe_redirect( remove_query_arg( array( 'esp-action', 'esp-notice', 'esp-dismiss-notice-nonce' ) ) );
	exit;

}
add_action( 'esp_dismiss_notices', 'esp_dismiss_notices', 10, 0 );

/**
 * Outputs the HTML for the rating notice admin prompt.
 *
 * We bug admins at certain intervals to rate the plugin.
 *
 * @since 1.0.0
 * @return void
 */
function esp_rating_notices() {

	// We only want admins.
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Get the plugin activation time & rating prompts intervals.
	$activation_time = get_site_option( 'esp_activation_time' );
	$prompts = (array) get_site_option( 'esp_rating_prompts' );

	// Sort the prompts to ensure they're in order.
	sort( $prompts, SORT_NUMERIC );

	foreach ( $prompts as $prompt ) {
		// Check if any of the prompts are greater than the activation time.
		if ( strtotime( $activation_time ) < strtotime( "-{$prompt} days" ) ) {

			// Show the rating prompt.
			$message = '<div class="notice notice-info">';
			$message .= '<p>' . esc_html__( "Hey, we noticed you've been using Email Summary Pro for a little while now – that’s brilliant! Could you please do me a BIG favor and give it a 5-star rating on WordPress? It really helps us spread the word and boosts our motivation.", 'email-summary-pro' ) . '</p>';
			$message .= '<p>- Edward</p>';
			$message .= '<p><a href="https://wordpress.org/support/plugin/email-summary-pro/reviews/" target="_blank">' . esc_html__( 'Sure, you deserve it', 'email-summary-pro' ) . '</a></p>';
			$message .= '<p><a href="' . wp_nonce_url( add_query_arg( array( 'esp-action' => 'dismiss_notices', 'esp-notice' => 'rating_permanent' ) ), 'esp-dismiss-notice', 'esp-dismiss-notice-nonce' ) . '">' . esc_html__( 'I already have', 'email-summary-pro' ) . '</a></p>';
			$message .= '<p><a href="' . wp_nonce_url( add_query_arg( array( 'esp-action' => 'dismiss_notices', 'esp-notice' => 'rating_temporary' ) ), 'esp-dismiss-notice', 'esp-dismiss-notice-nonce' ) . '">' . esc_html__( 'Nope, not right now', 'email-summary-pro' ) . '</a><p>';
			$message .= '</div>';

			// @codingStandardsIgnoreLine
			echo $message;

			// Break out of the foreach loop.
			break;
		}
	}

}
add_action( 'admin_notices', 'esp_rating_notices', 10, 0 );
