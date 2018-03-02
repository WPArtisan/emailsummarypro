<?php
/**
 * Summary class.
 *
 * General layout is inspired by EDD's Discount class..
 *
 * @package     email-summary-pro
 * @subpackage  Includes/Summary
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Summary class. Saves, deletes, applies etc.
 */
class Email_Summary_Pro_Summary {

	/**
	 * Summary ID.
	 *
	 * @var int
	 */
	public $ID;

	/**
	 * Summary name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Summary status.
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Summary recipients.
	 *
	 * @var string
	 */
	public $recipients;

	/**
	 * Summary disable_html_emails.
	 *
	 * @var string
	 */
	public $disable_html_emails;

	/**
	 * Class constructor.
	 *
	 * @param int $id ID of the current summary.
	 */
	public function __construct( $id = null ) {
		if ( empty( $id ) ) {
			return false;
		}

		$summary = WP_Post::get_instance( absint( $id ) );

		if ( $summary ) {
			$this->setup_summary( $summary );
		} else {
			return false;
		}
	}

	/**
	 * Gets a summary.
	 *
	 * @access public
	 * @param  WP_Post $summary WP_Post obhject to setup.
	 * @return object WPNA_Placement
	 */
	public function setup_summary( $summary = null ) {
		if ( null === $summary ) {
			return false;
		}

		if ( ! is_object( $summary ) ) {
			return false;
		}

		if ( is_wp_error( $summary ) ) {
			return false;
		}

		if ( ! is_a( $summary, 'WP_Post' ) ) {
			return false;
		}

		if ( 'esp_summary' !== get_post_type( $summary ) ) {
			return false;
		}

		/**
		 * Fires before the instance of the WPNA_Summary object is set up.
		 *
		 * @param object WPNA_Summary  WPNA_Summary instance of the summary object.
		 * @param object WP_Post $summary WP_Post instance of the summary object.
		 */
		do_action( 'esp_pre_setup_summary', $this, $summary );

		/**
		 * Setup all object variables
		 */
		$this->ID                  = absint( $summary->ID );
		$this->name                = $this->setup_name();
		$this->status              = $this->setup_status();
		$this->recipients          = $this->setup_recipients();
		$this->interval            = $this->setup_interval();
		$this->disable_html_emails = $this->setup_disable_html_emails();
		$this->next_scheduled      = $this->setup_next_scheduled();

		/**
		 * Fires after the instance of the WPNA_Summary object is set up.
		 *
		 * @param object WPNA_Summary      WPNA_Summary instance of the summary object.
		 * @param object WP_Post $summary WP_Post instance of the summary object.
		 */
		do_action( 'esp_setup_summary', $this, $summary );

		return true;
	}

	/**
	 * Setup the name of the summary.
	 *
	 * @access private
	 *
	 * @return string Name of the summary.
	 */
	private function setup_name() {
		$title = get_the_title( $this->ID );
		return $title;
	}

	/**
	 * Setup the status of the summary.
	 *
	 * @access private
	 *
	 * @return string Status of the summary.
	 */
	private function setup_status() {
		$status = get_post_status( $this->ID );
		return $status;
	}

	/**
	 * Setup the summary recipients.
	 *
	 * @access private
	 *
	 * @return string Summary recipients.
	 */
	private function setup_recipients() {
		$recipients = $this->get_meta( 'recipients', true );
		return $recipients;
	}

	/**
	 * Setup the summary interval.
	 *
	 * @access private
	 *
	 * @return string Summary interval.
	 */
	private function setup_interval() {
		$interval = $this->get_meta( 'interval', true );
		return $interval;
	}

	/**
	 * Setup the summary disable_html_emails.
	 *
	 * @access private
	 *
	 * @return string Summary disable_html_emails.
	 */
	private function setup_disable_html_emails() {
		$disable_html_emails = $this->get_meta( 'disable_html_emails', true );
		return $disable_html_emails;
	}

	/**
	 * Setup the summary next_scheduled.
	 *
	 * @access private
	 *
	 * @return string Summary disable_html_emails.
	 */
	private function setup_next_scheduled() {
		$next_scheduled = $this->get_meta( 'next_scheduled', true );
		return $next_scheduled;
	}

	/**
	 * Create a summary.
	 *
	 * @access public
	 * @param  array $args Arguments to save.
	 * @return object WPNA_Placement
	 */
	public function add( $args ) {

		if ( ! empty( $this->ID ) ) {
			return $this->update( $args );
		}

		/**
		 * Add a new summary to the database.
		 */

		$meta = $this->build_meta( $args );

		/**
		 * Filters the metadata before being inserted into the database.
		 *
		 * @param array $meta Summary meta.
		 * @param int   $ID   Summary ID.
		 */
		$meta = apply_filters( 'esp_insert_summary', $meta );

		/**
		 * Fires before the summary has been added to the database.
		 *
		 * @param array $meta Summary meta.
		 */
		do_action( 'edd_pre_insert_summary', $meta );

		$this->ID = wp_insert_post( array(
			'post_type'   => 'esp_summary',
			'post_title'  => $meta['name'],
			'post_status' => 'active',
		) );

		foreach ( $meta as $key => $value ) {
			$this->update_meta( $key, $value );
		}

		/**
		 * Fires after the summary code is inserted.
		 *
		 * @param array $meta {
		 *     The summary details.
		 *
		 *     @type string $name     The name of the summary.
		 *     @type string $status   The summary status. Defaults to active.
		 *     @type string $type     The type of ransformer.
		 *     @type string $selector The selector to apply the summary to.
		 *     @type string $rule     The summary rule to apply.
		 * }
		 * @param int $ID The ID of the summary that was inserted.
		 */
		do_action( 'esp_post_insert_summary', $meta, $this->ID );

		// Clear the cache.
		esp_clear_summary_cache();

		// Setup the summary again.
		$this->setup_summary( WP_Post::get_instance( $this->ID ) );

		return $this->ID;
	}

	/**
	 * Update an existing discount in the database.
	 *
	 * @since 2.7
	 * @access public
	 *
	 * @param array $args Discount details.
	 * @return mixed bool|int false if data isn't passed and class not instantiated for creation, or post ID for the new discount.
	 */
	public function update( $args ) {
		$meta = $this->build_meta( $args );

		/**
		 * Filter the data being updated
		 *
		 * @param array $meta Discount meta.
		 * @param int   $ID   Discount ID.
		 */
		$meta = apply_filters( 'esp_update_summary', $meta, $this->ID );

		/**
		 * Fires before the summary has been updated in the database.
		 *
		 * @param array $meta Summary meta.
		 * @param int   $ID   Summary ID.
		 */
		do_action( 'esp_pre_update_summary', $meta, $this->ID );

		wp_update_post( array(
			'ID'          => $this->ID,
			'post_title'  => $meta['name'],
			'post_status' => $meta['status'],
		) );

		foreach ( $meta as $key => $value ) {
			$this->update_meta( $key, $value );
		}

		// Clear the cache.
		esp_clear_summary_cache();

		// Setup the summary again.
		$this->setup_summary( WP_Post::get_instance( $this->ID ) );

		/**
		 * Fires after the summary has been updated in the database.
		 *
		 * @param array $meta Summary meta.
		 * @param int   $ID   Summary ID.
		 */
		do_action( 'esp_post_update_summary', $meta, $this->ID );

		return $this->ID;
	}

	/**
	 * Build Summary Meta Array.
	 *
	 * @access private
	 * @param array $args Summary meta.
	 * @return array Filtered and sanitized summary args.
	 */
	private function build_meta( $args ) {
		if ( ! is_array( $args ) || empty( $args ) ) {
			return null;
		}

		$meta = array(
			'name'                => ! empty( $args['name'] ) ? $args['name'] : '',
			'status'              => ! empty( $args['status'] ) ? $args['status'] : 'active',
			'recipients'          => ! empty( $args['recipients'] ) ? $args['recipients'] : '',
			'interval'            => ! empty( $args['interval'] ) ? $args['interval'] : 'weekly',
			'disable_html_emails' => ! empty( $args['disable_html_emails'] ) ? $args['disable_html_emails'] : '0',
		);

		// Work out the date of the next summary.
		// $start_of_week = get_option( 'start_of_week' );
		// $start_of_week_day = date( 'l', strtotime( "Sunday + {$start_of_week} Days" ) );
		// $next_summary = strtotime( date( 'Y-m-d 04:00:00', strtotime( 'next ' . $start_of_week_day ) ) );

		$meta = apply_filters( 'esp_summary_meta', $meta, $this->ID );

		return $meta;
	}

	/**
	 * Helper method to update post meta associated with the summary.
	 *
	 * @access public
	 * @param string $key        Meta key.
	 * @param string $value      Meta value.
	 * @param string $prev_value Previous meta value.
	 * @return int|bool Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	public function update_meta( $key = '', $value = '', $prev_value = '' ) {
		if ( empty( $key ) || '' === $key ) {
			return false;
		}

		$key = '_esp_summary_' . $key;

		$value = apply_filters( 'esp_update_summary_meta_' . $key, $value, $this->ID );

		return update_post_meta( $this->ID, $key, $value, $prev_value );
	}

	/**
	 * Helper method to retrieve meta data associated with the summary.
	 *
	 * @access public
	 * @param string $key    Meta key.
	 * @param bool   $single Return single item or array.
	 */
	public function get_meta( $key = '', $single = true ) {
		$meta = get_post_meta( $this->ID, '_esp_summary_' . $key, $single );
		return $meta;
	}
}
