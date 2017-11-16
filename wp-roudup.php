<?php
/**
 * Plugin Name: WP Roundup
 * Description: Fancy, succinct, slack-esq, weekly roundup email for your site.
 * Author: OzTheGreat (WPArtisan)
 * Author URI: https://wpartisan.me
 * Version: 0.0.1
 * Plugin URI: https://wpartisan.me/plugins/wp-roundup
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define the current version
define( 'WP_ROUNDUP_VERSION', '0.0.1' );

// Define the plugin base path
define( 'WP_ROUNDUP_BASE_PATH', dirname( __FILE__ ) );

// Define the plugin base URL
define( 'WP_ROUNDUP_BASE_URL', plugin_dir_url( __FILE__ ) );

/**
 * A global function that kicks everything off.
 * Really just to stop everything polluting the
 * global namespace.
 *
 * @return null
 */
function wp_roundup_initialise() {


	// Require the class if it doesn't exist
	// require WP_ROUNDUP_BASE_PATH . '/class-activator.php';

	// Register the plugin activation method
	// register_activation_hook( __FILE__, array( 'WP_ROUNDUP_Activator', 'run' ) );

	// Require the class if it doesn't exist
	// require WP_ROUNDUP_BASE_PATH . '/class-deactivator.php';

	// Register the plugin deactivation method
	// register_deactivation_hook( __FILE__, array( 'WP_ROUNDUP_Deactivator', 'run' ) );


	//
	// Global functions and helper files
	//

	// Load the helper functions
	require WP_ROUNDUP_BASE_PATH . '/includes/functions-helper.php';

	// Load the stats functions
	require WP_ROUNDUP_BASE_PATH . '/includes/functions-stats.php';

	/**
	 * Setup the global options array.
	 * Although they are stored in here they are never accessed
	 * directly, only through the helper functions.
	 */
	$GLOBALS['wp_roundup_options'] = wp_roundup_get_options();

	require WP_ROUNDUP_BASE_PATH . '/includes/class-admin-base.php';

	require WP_ROUNDUP_BASE_PATH . '/includes/class-helper-tabs.php';

	require WP_ROUNDUP_BASE_PATH . '/includes/class-roundup.php';

	// require WP_ROUNDUP_BASE_PATH . '/includes/class-stats-posts.php';
	// require WP_ROUNDUP_BASE_PATH . '/includes/class-stats-comments.php';
	// require WP_ROUNDUP_BASE_PATH . '/includes/class-stats-users.php';
	//
	// require WP_ROUNDUP_BASE_PATH . '/includes/class-layout-formatter.php';

	require WP_ROUNDUP_BASE_PATH . '/includes/class-admin.php';
	$wp_roundup_admin = new WP_Roundup_Admin();


	// require WP_ROUNDUP_BASE_PATH . '/includes/class-schedule.php';
	// $wp_roundup = new WP_Roundup_Schedule()

}

wp_roundup_initialise();


## https://core.trac.wordpress.org/ticket/15448


// Error logging
// add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
// function log_mailer_errors(){
// $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
// $fp = fopen($fn, 'a');
// fputs($fp, "Mailer Error: " . $mailer->ErrorInfo ."\n");
// fclose($fp);
// }
