<?php
/**
 * Pieces together the content for the roundup email.
 *
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 * @since 1.0.0
 */
class Email_Summary_Pro_Email {

	protected $data;
	protected $date_from;
	protected $date_to;

	public function __construct( $summary = null ) {

		// Make sure there's a default date value.
		// $this->date();
	}

	/**
	 * Work out the dates to build the email for.
	 * Default to latest. Can pass any strtotime() compatible
	 * date in and it will build the email for that week.
	 *
	 * @param  string $date
	 * @return void
	 */
	public function date( $date = 'latest' ) {
		$start_of_week = get_option( 'start_of_week' );
		$start_of_week_day = date( 'l', strtotime( "Sunday + {$start_of_week} Days" ) );

		if ( 'latest' == $date ) {
			$unix_timestamp = strtotime( "last " . $start_of_week_day );
		} else {
			$unix_timestamp = strtotime( $date );
		}

		// The date the newsletter was sent out. Normally one day after.
		$this->date = date( "Y-m-d", $unix_timestamp );

		// The date we want stats from
		$this->date_from = date( "Y-m-d", strtotime( "Last " . $start_of_week_day, $unix_timestamp ) );

		// The date we want stats to (inclusive)
		$this->date_to = date( "Y-m-d", strtotime( "+ 6 days ", strtotime( $this->date_from ) ) );
	}

	/**
	 * Constructs the email template.
	 * Sets up global variables to be used in the templates.
	 * Constructs both plain and HTML variavles.
	 *
	 * @param  string $type plains or HTML templates to build
	 * @return string
	 */
	public function get_template( $type = 'plain' ) {

		// Ensure type is something we're expecting.
		$type = ( 'plain' === $type ? 'plain' : 'html' );

		// Setup the default template parts for every email.
		$default_template_parts = array(
			$type . '/parts/pre-content',
			$type . '/parts/logo',
			$type . '/parts/branding',
			$type . '/parts/introduction',
			$type . '/parts/posts',
			$type . '/parts/data-table',
			$type . '/parts/comments',
			$type . '/parts/users',
			$type . '/parts/updates',
			$type . '/parts/signoff',
		);

		/**
		 * Register template parts.
		 *
		 * Use this hook to register template parts for template types.
		 * It should be an array of short links to template parts relative to the
		 * templates directory.
		 *
		 * e.g. 'html/parts/introduction' or 'plain/parts/jetpack-comments'
		 *
		 * @since 1.0.0
		 *
		 * @var array $default_template_parts Location of template part to include.
		 * @var string $type The type of template we're dealing with.
		 */
		$template_parts = apply_filters( 'esp_template_parts-' . $type, $default_template_parts, $type );

		// Setup the default template variables for every email.
		$template_arguments = array(
			'template_parts'    => $template_parts,
			'summary_date'      => $this->date, // The roundup is sent the day after the week ends.
			'summary_date_from' => $this->date_from, // The first date of the week we're rounding up.
			'summary_date_to'   => $this->date_to, // The last date of the week we're rounding up (inclusive).
		);

		/**
		 * Global arguments available inside every template.
		 *
		 * @var array
		 */
		$template_arguments = apply_filters( 'esp_template_arguments-' . $type, $template_arguments, $type );

		// Check the tempalte exists before trying to get it
		if ( ! file_exists( esp_locate_template( $type . '/main' ) ) ) {
			return false;
		}

		ob_start();

		// Load the template.
		// All content is done through the template.
		esp_get_template( $type . '/main.php', $template_arguments );

		// Read the contents into a variable.
		$template = ob_get_contents();

		// Turn off output buffering
		ob_end_clean();

		/**
		 * Use to filter the complete content string.
		 *
		 * @since 1.0.0
		 * @var string $template
		 */
		$template = apply_filters( 'esp_email_content-' . $type, $template );

		return $template;
	}

	/**
	 * Sends the email.
	 *
	 * Puts everything together and does the sending of the email.
	 *
	 * @access public
	 * @return null
	 */
	public function send() {

		/**
		 * The recipients of the roundup email.
		 * Should be a comma seperated list.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		$to = apply_filters( 'esp_email_to', esp_get_option( 'recipients' ) );

		// Bail if no recipient is set
		if ( empty( $to ) ){
			return;
		}

		/**
		 * Subject for the summary email.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		$subject = apply_filters( 'esp_email_subject', esc_html__( 'Weekly Roundup', 'wp-roundup' ) );

		// Create a boundary for the email.
		$boundary = uniqid( 'np' );

		// If HTML emails are disabled, send a plain one.
		if ( esp_get_option( 'disable_html_emails' ) ) {
			$message = $this->get_template( 'plain' );
		} else {
			//here is the content body
			$message = "This is a MIME encoded message.";

			// Set the plain text boundary.
			$message .= PHP_EOL . PHP_EOL . "--" . $boundary . PHP_EOL;
			$message .= "Content-type: text/plain;charset=utf-8" . PHP_EOL . PHP_EOL;

			// Plain text body.
			$message .= $this->get_template( 'plain' );

			// Set the HTML boundary.
			$message .= PHP_EOL . PHP_EOL . "--" . $boundary . PHP_EOL;
			$message .= "Content-type: text/html;charset=utf-8" . PHP_EOL . PHP_EOL;

			//Html body
			$message .= $this->get_template( 'html' );

			// Close the boundary.
			$message .= PHP_EOL . PHP_EOL . "--" . $boundary . "--";
		}

		// Email headers
		$headers = array(
			"MIME-Version: 1.0",
			sprintf( "From: %s <%s>", get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ) ),
			sprintf( "boundary=%s", $boundary ),
		);

		/**
		 * Headers for the summary email.
		 *
		 * @var array $headers
		 */
		$headers = apply_filters( 'esp_email_headers', $headers );

		/**
		 * Attachments for the summary email.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$attachments = apply_filters( 'esp_email_headers', array() );

		// Make sure the email content type is set.
		add_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ), 10, 1 );

		// Make sure we're logging any errors.
		add_action( 'wp_mail_failed', array( $this, 'log_errors' ), 10, 1 );

		/**
		 * Run directly before the summary is sent.
		 *
		 */
		do_action( 'esp_before_wp_mail', $to, $subject, $message, $headers, $attachments );

		// Send the email.
		wp_mail( $to, $subject, $message, $headers, $attachments );

		/**
		 * Run directly after the summary is sent.
		 *
		 */
		do_action( 'eso_after_wp_mail', $to, $subject, $message, $headers, $attachments );

		// Remove the action for logging errors.
		remove_action( 'wp_mail_failed', array( $this, 'log_errors' ), 10 );

		// Remove the filter for changing the email content type.
		remove_filter( 'wp_mail_content_type', array( $this, 'set_content_type' ), 10 );
	}

	/**
	 * Log any errors with the mailer when sending the emails.
	 *
	 * @access public
	 * @param WP_Error $wp_error What went wrong.
	 * @return void
	 */
	public function log_errors( $wp_error ) {
		// Get any old errors.
		$errors = (array) get_transient( 'esp_email_errors' );

		// Add the error message in,
		$errors[] = $wp_error->get_error_message();

		// Add it to the transient.
		set_transient( 'esp_email_errors', $errors );
	}

	/**
	 * Set the content type for the email.
	 * Default to multipart/alternative.
	 * If HTML has been disabled switch to text/plain.
	 *
	 * @access public
	 * @param string $content_type Email message format.
	 * @return string
	 */
	public function set_content_type( $content_type ) {
		if ( esp_get_option( 'disable_html_emails' ) ) {
			return 'text/plain';
		}

		return 'multipart/alternative';
	}

}
