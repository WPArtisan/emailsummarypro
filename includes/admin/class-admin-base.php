<?php
/**
 * Base admin class for easy extending.
 *
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * A base class for setting up new admin pages.
 *
 * All plugin admin pages extend this base class.
 * Will trigger the hooks method and setup anything
 * necessary for the admin page.
 *
 * @since 1.0.0
 */
abstract class Email_Summary_Pro_Admin_Base {

	/**
	 * Holds the slug of the current page.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public $page_slug;

	/**
	 * Constructor.
	 *
	 * Triggers the hooks method straight away.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Return the full url of the current admin page.
	 *
	 * Uses the page_slug class variable to construct the full URL to the
	 * current admin plugin page. Custom query args can be passed in as well.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param  array  Optional. Query args to add to the URL. Default none
	 * @return string The full URL to the current admin page
	 */
	public function page_url( $params = array() ) {
		$query_args = array_merge( array( 'page' => $this->page_slug ), $params );
		return add_query_arg( $query_args );
	}

}
