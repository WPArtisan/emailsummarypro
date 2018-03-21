<?php
/**
 * Summary class.
 *
 * General layout is inspired by EDD's Discount class.
 *
 * @package     email-summary-pro
 * @subpackage  Includes/Summary
 * @copyright   Copyright (c) 2018, WPArtisan
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
	 * Default to email.
	 *
	 * @var string
	 */
	public $method = 'email';

	/**
	 * Summary recipients.
	 *
	 * @var string
	 */
	public $recipients;

	/**
	 * Summary email subject.
	 *
	 * @var string
	 */
	public $subject;

	/**
	 * Summary disable_html.
	 *
	 * @var string
	 */
	public $disable_html;

	/**
	 * The date of the summary in ISO8601 format.
	 * Defaults to today but could be in the past if re-sending.
	 *
	 * @var string
	 */
	public $date;

	/**
	 * The start date of the summary in ISO8601 format.
	 *
	 * @var string
	 */
	public $date_from;

	/**
	 * The end date of the summary in ISO8601 format.
	 *
	 * @var string
	 */
	public $date_to;

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
	 * @param  WP_Post $summary WP_Post object to setup.
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
		$this->ID             = absint( $summary->ID );
		$this->name           = $this->setup_name();
		$this->status         = $this->setup_status();
		$this->recipients     = $this->setup_recipients();
		$this->subject        = $this->setup_subject();
		$this->title_raw      = $this->setup_title_raw();
		$this->title          = $this->setup_title();
		$this->interval       = $this->setup_interval();
		$this->template       = $this->setup_template();
		$this->disable_html   = $this->setup_disable_html();
		$this->next_scheduled = $this->setup_next_scheduled();
		$this->date           = $this->setup_date();
		$this->date_from      = $this->setup_date_from();
		$this->date_to        = $this->setup_date_to();

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
	 * Used of admin identification only.
	 *
	 * @access private
	 * @return string Name of the summary.
	 */
	private function setup_name() {
		$name = get_the_title( $this->ID );

		/**
		 * Filter the name used for this summary.
		 *
		 * @var string $name The name (main title) used for this summary.
		 * @var object $this The current summary.
		 */
		$name = apply_filters( 'esp_summary_name', $name, $this );

		return $name;
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

		/**
		 * Filter the status of this summary.
		 *
		 * @var string $status The status of this summary.
		 * @var object $this The current summary.
		 */
		$status = apply_filters( 'esp_summary_status', $status, $this );

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

		/**
		 * Filter the recipients of this summary.
		 *
		 * @var string $recipients The recipients of this summary.
		 * @var object $this The current summary.
		 */
		$recipients = apply_filters( 'esp_summary_recipients', $recipients, $this );

		return $recipients;
	}

	/**
	 * Setup the summary subject.
	 *
	 * @access private
	 *
	 * @return string Summary subject.
	 */
	private function setup_subject() {
		$subject = $this->get_meta( 'subject', true );

		/**
		 * Filter the subject of this summary.
		 *
		 * @var string $subject The subject of this summary.
		 * @var object $this The current summary.
		 */
		$subject = apply_filters( 'esp_summary_subject', $subject, $this );

		return $subject;
	}

	/**
	 * Setup the raw title for the summary.
	 * No placeholders will be replaced.
	 *
	 * @access private
	 * @return string Title of the summary.
	 */
	private function setup_title_raw() {
		$title = $this->get_meta( 'title', true );

		/**
		 * Filter the title of this summary.
		 *
		 * @var string $name The title of this summary.
		 * @var object $this The current summary.
		 */
		$title = apply_filters( 'esp_summary_title_raw', $title, $this );

		return $title;
	}

	/**
	 * Setup the title for the summary.
	 * With all placeholders replaced.
	 *
	 * @access private
	 * @return string Title of the summary.
	 */
	private function setup_title() {
		$title = $this->get_meta( 'title', true );

		$title_placeholders = array(
			'site_name'         => get_bloginfo( 'name' ),
			'site_description'  => get_bloginfo( 'description' ),
			'site_url'          => get_bloginfo( 'url' ),
			'blog_id'           => get_current_blog_id(),
		);

		/**
		 * Add or remove any title placeholders using this hook.
		 *
		 * @param array $title_placeholders Placeholders for the title.
		 * @var object $this The current summary.
		 */
		$title_placeholders = apply_filters( 'esp_title_placeholders', $title_placeholders, $this );

		// Replace all the arguments.
		foreach ( $title_placeholders as $key => $value ) {
			$title = str_replace( sprintf( '{%s}', $key ), $value, $title );
		}

		/**
		 * Filter the title of this summary.
		 *
		 * @var string $name The title of this summary.
		 * @var object $this The current summary.
		 */
		$title = apply_filters( 'esp_summary_title', $title, $this );

		return $title;
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

		/**
		 * Filter the interval of this summary.
		 *
		 * @var string $interval The interval of this summary.
		 * @var object $this The current summary.
		 */
		$interval = apply_filters( 'esp_summary_interval', $interval, $this );

		return $interval;
	}

	/**
	 * Setup the summary template.
	 *
	 * @access private
	 *
	 * @return string Summary template.
	 */
	private function setup_template() {
		$template = $this->get_meta( 'template', true );

		/**
		 * Filter the template of this summary.
		 *
		 * @var string $template The template of this summary.
		 * @var object $this The current summary.
		 */
		$template = apply_filters( 'esp_summary_template', $template, $this );

		return $template;
	}

	/**
	 * Setup the summary disable_html.
	 *
	 * @access private
	 *
	 * @return string Summary disable_html.
	 */
	private function setup_disable_html() {
		$disable_html = $this->get_meta( 'disable_html', true );

		/**
		 * Filter disable_html of this summary.
		 *
		 * @var string $disable_html The disable_html of this summary.
		 * @var object $this The current summary.
		 */
		$disable_html = apply_filters( 'esp_summary_disable_html', $disable_html, $this );

		return $disable_html;
	}

	/**
	 * Setup the date variable.
	 *
	 * If one has been set then use the summary from that week / day.
	 * Otherwise default to last summary's.
	 *
	 * @return string The date of the summary.
	 */
	private function setup_date() {
		if ( empty( $this->date ) ) {
			$this->date = date( DateTime::ISO8601 );
		}

		return $this->date;
	}

	/**
	 * Setup the start date of the summary stats.
	 *
	 * @access private
	 *
	 * @return int Unix timestamp.
	 */
	private function setup_date_from() {
		// Default to the current date/time.
		$this->date_from = $this->date;

		if ( 'weekly' === $this->interval ) {
			$start_of_week     = get_option( 'start_of_week' );
			$start_of_week_day = date( 'l', strtotime( "Sunday + {$start_of_week} Days" ) );
			$this->date_from   = date( DateTime::ISO8601, strtotime( 'Last ' . $start_of_week_day, strtotime( $this->date ) ) );
		}

		return $this->date_from;
	}

	/**
	 * Setup the last date of the summary stats.
	 *
	 * @access private
	 *
	 * @return int UNix timestamp.
	 */
	private function setup_date_to() {
		// Default to the current date/time.
		$this->date_to = $this->date;

		if ( 'weekly' === $this->interval ) {
			$this->date_to = date( DateTime::ISO8601, strtotime( '+ 6 days ', strtotime( $this->date_from ) ) );
		}

		return $this->date_to;
	}

	/**
	 * Setup the summary next_scheduled.
	 *
	 * @access private
	 *
	 * @return string Summary disable_html.
	 */
	private function setup_next_scheduled() {

		// If a CRON schedule is already setup return that.
		if ( $next_scheduled = wp_next_scheduled( 'esp_do_summary', $this->ID ) ) {
			return date( DateTime::ISO8601, $next_scheduled );
		}

		if ( 'weekly' === $this->interval ) {
			$start_of_week     = get_option( 'start_of_week' );
			$start_of_week_day = date( 'l', strtotime( "Sunday + {$start_of_week} Days" ) );
			$next_scheduled    = strtotime( 'next ' . $start_of_week_day );
			$next_scheduled    = strtotime( '+4 hours ', $next_scheduled ); // Do it at 4 in the morning.
			$next_scheduled    = date( DateTime::ISO8601, $next_scheduled );
		}

		if ( 'inactive' === $this->status ) {
			$next_scheduled = null;
		}

		$next_scheduled = apply_filters( 'esp_summary_setup_next_scheduled', $next_scheduled, $this );

		return $next_scheduled;
	}

	/**
	 * Set the date of a the summary you want to run.
	 * Default to the nearest previous one.
	 *
	 * @param string $date Date you want the summary for.
	 */
	public function set_date( $date ) {
		// Make sure the date is in ISO8601 format.
		$this->date = date( DateTime::ISO8601, strtotime( $date ) );

		// Re-setup the date_to and date_from with the new date.
		$this->setup_date_from();
		$this->setup_date_to();
	}

	/**
	 * Send the summary summary.
	 *
	 * @access public
	 * @return object WPNA_Placement
	 */
	public function send() {
		$method = null;

		if ( 'email' === $this->method ) {
			$method = new Email_Summary_Pro_Email( $this );
		}

		/**
		 * Change the method used to send this summary.
		 * Or kill it altogether.
		 *
		 * @var object
		 * @var object Email_Summary_Pro_Summary
		 */
		$method = apply_filters( 'esp_summary_method', $method, $this );

		if ( $method  ) {
			$method->send( $this );
		}
	}

	/**
	 * Schedule the summary to send.
	 *
	 * @access public
	 * @return void
	 */
	public function schedule() {
		if ( empty( $this->ID ) ) {
			return;
		}

		// Clear any currently scheduled events incase the time has changed.
		// Or incase they've been made inactive.
		if ( $next_scheduled = wp_next_scheduled( 'esp_do_summary', $this->ID ) ) {
			// Remove the CRON hook.
			wp_unschedule_event( $next_scheduled, 'esp_do_summary', $this->ID );
			// Re-setup the next scheduled.
			$this->setup_next_scheduled();
		}

		// Check it's active.
		if ( 'active' === $this->status ) {
			// Schedue a new one.
			wp_schedule_event( strtotime( $this->next_scheduled ), $this->interval, 'esp_do_summary', array( $this->ID ) );
		}
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
		do_action( 'esp_pre_insert_summary', $meta );

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

		// And make sure it's scheduled.
		$this->schedule();

		return $this->ID;
	}

	/**
	 * Update an existing summary in the database.
	 *
	 * @access public
	 * @param array $args Summary details.
	 * @return mixed bool|int false if data isn't passed and class not instantiated for creation, or post ID for the new summary.
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

		// And make sure it's scheduled.
		$this->schedule();

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
	 * Update the status of the summary.
	 *
	 * @access public
	 * @param string $new_status New status (default: active)
	 * @return bool If the status been updated or not.
	 */
	public function update_status( $new_status = 'active' ) {
		/**
		 * Fires before the status of the summary is updated.
		 *
		 * @param int    $ID          Discount ID.
		 * @param string $new_status  New status.
		 * @param string $post_status Post status.
		 */
		do_action( 'esp_pre_update_summary_status', $this->ID, $new_status, $this->status );

		$id = wp_update_post(
			array(
				'ID'          => $this->ID,
				'post_status' => $new_status
			)
		);

		// Clear the cache.
		esp_clear_summary_cache();

		// Setup the summary again.
		$this->setup_summary( WP_Post::get_instance( $this->ID ) );

		// And make sure it's scheduled or unscheduled.
		$this->schedule();

		/**
		 * Fires after the status of the summary is updated.
		 *
		 * @param int    $ID          Discount ID.
		 * @param string $new_status  New status.
		 * @param string $post_status Post status.
		 */
		do_action( 'esp_post_update_summary_status', $this->ID, $new_status, $this->status );

		if ( $id == $this->ID ) {
			return true;
		}

		return false;
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
			'name'         => ! empty( $args['name'] ) ? $args['name']                 : '',
			'status'       => ! empty( $args['status'] ) ? $args['status']             : 'active',
			'method'       => ! empty( $args['method'] ) ? $args['method']             : 'email',
			'recipients'   => ! empty( $args['recipients'] ) ? $args['recipients']     : '',
			'subject'      => ! empty( $args['subject'] ) ? $args['subject']           : '',
			'title'        => ! empty( $args['title'] ) ? $args['title']               : '',
			'interval'     => ! empty( $args['interval'] ) ? $args['interval']         : 'weekly',
			'template'     => ! empty( $args['template'] ) ? $args['template']         : 'html',
			'disable_html' => ! empty( $args['disable_html'] ) ? $args['disable_html'] : '0',
		);

		/**
		 * Filter the meta that gets saved for the summary.
		 *
		 * @var $meta array of meta to save.
		 * @var $args array of form data that was passed to the summary.
		 * @var ID of the current summary.
		 */
		$meta = apply_filters( 'esp_summary_meta', $meta, $args, $this->ID );

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
