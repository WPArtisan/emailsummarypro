<?php
/**
 * Set up the summary post type & the custom status.
 *
 * @package     email-summary-pro
 * @subpackage  Includes
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Add in the Summaries post type.
 *
 * @return void
 */
function esp_setup_summaries_post_type() {

	/** Summaries Post Type */
	$summary_labels = array(
		'name'               => _x( 'Summaries', 'post type general name', 'email-summary-pro' ),
		'singular_name'      => _x( 'Summary', 'post type singular name', 'email-summary-pro' ),
		'add_new'            => __( 'Add New', 'email-summary-pro' ),
		'add_new_item'       => __( 'Add New Summary', 'email-summary-pro' ),
		'edit_item'          => __( 'Edit Summary', 'email-summary-pro' ),
		'new_item'           => __( 'New Summary', 'email-summary-pro' ),
		'all_items'          => __( 'All Summaries', 'email-summary-pro' ),
		'view_item'          => __( 'View Summary', 'email-summary-pro' ),
		'search_items'       => __( 'Search Summaries', 'email-summary-pro' ),
		'not_found'          => __( 'No Summaries found', 'email-summary-pro' ),
		'not_found_in_trash' => __( 'No Summaries found in Trash', 'email-summary-pro' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Summaries', 'email-summary-pro' ),
	);

	$summary_args = array(
		'labels'          => apply_filters( 'esp_summary_labels', $summary_labels ),
		'public'          => false,
		'query_var'       => false,
		'rewrite'         => false,
		'show_ui'         => false,
		'capability_type' => 'manage_options',
		'map_meta_cap'    => true,
		'supports'        => array( 'title' ),
		'can_export'      => true,
	);
	register_post_type( 'esp_summary', $summary_args );
}
add_action( 'init', 'esp_setup_summaries_post_type', 1 );

/**
 * Add in the Summaries custom post status.
 *
 * @return void
 */
function esp_register_post_type_statuses() {
	// Discount Code Statuses
	register_post_status( 'active', array(
		'label'                     => _x( 'Active', 'Active summary code status', 'email-summary-pro' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'email-summary-pro' )
	)  );
	register_post_status( 'inactive', array(
		'label'                     => _x( 'Inactive', 'Inactive summary code status', 'email-summary-pro' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'email-summary-pro' )
	)  );
}
add_action( 'init', 'esp_register_post_type_statuses', 2 );
