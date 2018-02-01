<?php
/**
 * Sets up the CRON for scheduling the digest emails
 *
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 */
class Email_Summary_Pro_Schedule {


	public function __construct() {
		add_action( 'admin_init',     array( $this, 'setup_cron' ), 10 );
		add_action( 'esp_cron_hook',  array( $this, 'do_summary' ), 10 );

		add_filter( 'cron_schedules', array( $this, 'add_cron_intervals' ), 10, 1 );
	}

	/**
	 * Make sure there's a weekly CRON schedule.
	 *
	 * @param array $schedules
	 * @return array CRON intervals
	 */
	public function add_cron_intervals( $schedules ) {
		// Add a 'weekly' interval.
		$schedules['weekly'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => esc_html__( 'Once Weekly', 'email-summary-pro' ),
		);

		return $schedules;
	}

	public function setup_cron() {
		// Work out the date of the next summary.
		$start_of_week = get_option( 'start_of_week' );
		$start_of_week_day = date( 'l', strtotime( "Sunday + {$start_of_week} Days" ) );
		$next_summary = strtotime( date( 'Y-m-d 04:00:00', strtotime( 'next ' . $start_of_week_day ) ) );

		if ( ! wp_next_scheduled( 'esp_cron_hook' ) ) {
			wp_schedule_event( $next_summary, 'weekly', 'esp_cron_hook' );
		}
	}

	/**
	 * Send a new summary. It defaults to the latest week.
	 *
	 * @access public
	 * @return void
	 */
	public function do_summary() {
		// Setup new email summary and send it.
		$summary = new Email_Summary_Pro_Email();
		$summary->send();
	}
}
