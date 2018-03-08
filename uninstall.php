<?php
/**
 * Handles uninstalling the plugin
 *
 * When the plugin is removed make sure it cleans up after itself.
 *
 * @package     email-summary-pro
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Removes all traces of the plugin from this site.
 *
 * @return void
 */
function esp_uninstall() {

	// Don't run this untill we make it optional.
	return;

	// This is a much more reliable way of completely
	// clearing the CRON hook. If there are any orphaned
	// rows it will ensure they're removed as well.
	$crons = _get_cron_array();

	foreach ( $crons as $cron ) {
		if ( isset( $cron['esp_do_summary'] ) ) {
			foreach ( $cron['esp_do_summary'] as $esp_cron ) {
				wp_clear_scheduled_hook( 'esp_do_summary', $esp_cron['args'] );
			}
		}
	}

	// Get all the stored summaries.
	$summaries = get_posts(
		array(
			'post_type'   => 'esp_summary',
			'numberposts' => -1,
		)
	);
	// Remove each post.
	foreach ( $summaries as $summary ) {
		// Delete each post.
		wp_delete_post( $summary->ID, true );
	}

	// Clear the summary cache as well.
	if ( function_exists( 'esp_clear_summary_cache' ) ) {
		esp_clear_summary_cache();
	}

	// Delete all the stored options.
	delete_option( 'esp_activation_time' );
	delete_option( 'esp_rating_prompts' );
	delete_option( 'esp_db_version' );
}
