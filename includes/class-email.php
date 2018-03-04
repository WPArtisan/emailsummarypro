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


	public $to;

	public $subject;

	public $body;

	public $disable_html;

	public function __construct( $summary ) {
		$this->to           = $summary->recipients;
		$this->subject      = $summary->subject;
		$this->body         = $summary->content;
		$this->disable_html = $summary->disable_html;
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
		$this->to = apply_filters( 'esp_email_to', $this->to );

		// Bail if no recipient is set
		if ( empty( $this->to ) ){
			return;
		}

		/**
		 * Subject for the summary email.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		$this->subject = apply_filters( 'esp_email_subject', $this->subject );

		// Create a boundary for the email.
		$boundary = uniqid( 'np' );

		// If HTML emails are disabled, send a plain one.
		if ( ! $this->disable_html ) {
			$message = $this->body;
		} else {
			//here is the content body
			$message = 'This is a MIME encoded message.';

			// Set the plain text boundary.
			$message .= PHP_EOL . PHP_EOL . '--' . $boundary . PHP_EOL;
			$message .= 'Content-type: text/plain;charset=utf-8' . PHP_EOL . PHP_EOL;

			// Plain text body.
			$message .= $this->get_template( 'plain' );

			// Set the HTML boundary.
			$message .= PHP_EOL . PHP_EOL . '--' . $boundary . PHP_EOL;
			$message .= 'Content-type: text/html;charset=utf-8' . PHP_EOL . PHP_EOL;

			//Html body
			$message .= $this->body;

			// Close the boundary.
			$message .= PHP_EOL . PHP_EOL . '--' . $boundary . '--';
		}

		// Email headers
		$headers = array(
			'MIME-Version: 1.0',
			sprintf( 'From: %s <%s>', get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ) ),
			sprintf( 'boundary=%s', $boundary ),
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
		do_action( 'esp_before_wp_mail', $this->to, $this->subject, $message, $headers, $attachments );

		// Send the email.
		wp_mail( $this->to, $this->subject, $message, $headers, $attachments );

		/**
		 * Run directly after the summary is sent.
		 *
		 */
		do_action( 'eso_after_wp_mail', $this->to, $this->subject, $message, $headers, $attachments );

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
