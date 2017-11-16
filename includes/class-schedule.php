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
class WP_Roundup_Schedule {


	public function hooks() {

		add_filter( 'cron_schedules', array( $this, 'add_cron_intervals' ), 10, 1 );
	}

	/**
	 * [add_cron_intervals description]
	 * @param array $schedules [description]
	 * @return array
	 */
	public function add_cron_intervals( $schedules ) {

		// add a 'weekly' interval
		$schedules['weekly'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => __( 'Once Weekly', 'wp-roundup' );
		);

		return $schedules;
	}

}
