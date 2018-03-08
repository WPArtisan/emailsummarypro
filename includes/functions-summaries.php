<?php
/**
 * Generic Summary functions.
 *
 * @package     email-summary-pro
 * @subpackage  Includes/Summaries
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'esp_get_summary' ) ) :

	/**
	 * Retrieves a summary from the DB.
	 *
	 * @param int $summary_id ID of the summary to retrieve.
	 * @return object Email_Summary_Pro_Summary
	 */
	function esp_get_summary( $summary_id ) {
		$summary = new Email_Summary_Pro_Summary( $summary_id );
		return $summary;
	}
endif;

if ( ! function_exists( 'esp_get_summaries' ) ) :

	/**
	 * Retrieves a summary from the DB.
	 *
	 * @param array $args Custom WP_Query arguments for retrieving summaries.
	 * @return object Email_Summary_Pro_Summary
	 */
	function esp_get_summaries( $args = array() ) {
		$defaults = array(
			'post_type'      => 'esp_summary',
			'posts_per_page' => 30,
			'paged'          => null,
			'post_status'    => array( 'active', 'inactive' ),
		);

		$args = wp_parse_args( $args, $defaults );

		// Workout the cache key.
		$cache_key = md5( json_encode( $args ) );

		// Check the key.
		// if ( $summaries = wp_cache_get( $cache_key, 'esp' ) ) {
		// 	return $summaries;
		// }

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() && ! empty( $args['s'] ) ) {
			// Add in the meta search.
			// @codingStandardsIgnoreStart
			$args['meta_key']     = '_esp_summary_recipients';
			$args['meta_value']   = $args['s'];
			$args['meta_compare'] = 'LIKE';
			// Remove the search.
			$args['s'] = null;
			// @codingStandardsIgnoreEnd

			// Try again.
			$query = new WP_Query( $args );
		}

		if ( ! $query->have_posts() ) {
			return null;
		}

		$summaries = array();
		foreach ( $query->get_posts() as $key => $summary ) {
			$summaries[ $key ] = new Email_Summary_Pro_Summary( $summary->ID );
		}

		// Set the cache.
		wp_cache_set( $cache_key, $summaries, 'esp' );

		// Update the cache keys.
		$cache_keys = wp_cache_get( 'esp_summaries_cache_keys', 'esp' );

		$cache_keys[] = $cache_key;

		$cache_keys = array_unique( $cache_keys );
		$cache_keys = array_filter( $cache_keys );

		wp_cache_set( 'esp_summaries_cache_keys', $cache_keys, 'esp' );

		return $summaries;
	}
endif;

if ( ! function_exists( 'esp_add_summary' ) ) :

	/**
	 * Inserts a summary into the DB.
	 *
	 * @param array $data Data to be inserted.
	 * @return mixed. ID on success, false on failure.
	 */
	function esp_add_summary( $data ) {
		$summary = new Email_Summary_Pro_Summary();
		return $summary->add( $data );
	}
endif;

if ( ! function_exists( 'esp_update_summary' ) ) :

	/**
	 * Updates a summary already in the DB.
	 *
	 * @param int   $summary_id ID of the summary to update.
	 * @param array $data Data to be inserted.
	 * @return mixed. ID on success, false on failure.
	 */
	function esp_update_summary( $summary_id, $data ) {
		$summary = new Email_Summary_Pro_Summary( $summary_id );
		return $summary->update( $data );
	}
endif;

if ( ! function_exists( 'esp_delete_summary' ) ) :

	/**
	 * Removes a summary completely.
	 *
	 * @param int $summary_id ID of the summary to remove.
	 * @return void
	 */
	function esp_delete_summary( $summary_id ) {
		do_action( 'esp_pre_delete_summary', $summary_id );

		// Clear any currently scheduled events for this summary.
		if ( $next_scheduled = wp_next_scheduled( 'esp_do_summary', $summary_id ) ) {
			wp_unschedule_event( $next_scheduled, 'esp_do_summary', $summary_id );
		}

		// Delete the summary.
		wp_delete_post( $summary_id, true );

		// Clear the summary cache.
		esp_clear_summary_cache();

		do_action( 'esp_post_delete_summary', $summary_id );
	}
endif;

if ( ! function_exists( 'esp_update_summary_status' ) ) :

	/**
	 * Updates a summary's status from one status to another.
	 *
	 * @param int    $code_id    Summary ID (default: 0)
	 * @param string $new_status New status (default: active)
	 * @return bool Whether the status has been updated or not.
	 */
	function esp_update_summary_status( $summary_id = 0, $new_status = 'active' ) {
		$updated = false;
		$summary = esp_get_summary( $summary_id );

		if ( $summary && $summary->ID > 0 ) {
			$updated = $summary->update_status( $new_status );
		}

		return $updated;
	}
endif;

if ( ! function_exists( 'esp_clear_summary_cache' ) ) :

	/**
	 * Clear all the summary cache.
	 *
	 * @return void
	 */
	function esp_clear_summary_cache() {
		// Get all the cache keys.
		$cache_keys = wp_cache_get( 'esp_summaries_cache_keys', 'esp' );

		if ( $cache_keys && is_array( $cache_keys ) ) {
			foreach ( $cache_keys as $cache_key ) {
				wp_cache_delete( $cache_key, 'esp' );
			}
		}

		// Clear the cache keys as well.
		wp_cache_delete( 'esp_summaries_cache_keys', 'esp' );
	}
endif;
