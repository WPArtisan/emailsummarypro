<?php
/**
 * Site Actions
 *
 * @package     email-summary-pro
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Processes all Email Summary Pro actions sent via
 * POST and GET by looking for the 'esp-action' action.
 * Run do_action() to call the function.
 *
 * Credit: Easy Digital Downloads.
 *
 * @return void
 */
function esp_process_actions() {
	// @codingStandardsIgnoreStart
	if ( isset( $_POST['esp-action'] ) ) {
		do_action( 'esp_' . sanitize_key( wp_unslash( $_POST['esp-action'] ) ), $_POST );
	}

	if ( isset( $_GET['esp-action'] ) ) {
		do_action( 'esp_' . sanitize_key( wp_unslash( $_GET['esp-action'] ) ), $_GET );
	}
	// @codingStandardsIgnoreEnd
}
add_action( 'init', 'esp_process_actions' );
