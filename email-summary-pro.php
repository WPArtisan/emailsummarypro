<?php
/**
 * Plugin Name: Email Summary Pro
 * Description: Fancy, succinct, slack-esq, weekly roundup email for your site.
 * Author: OzTheGreat (WPArtisan)
 * Author URI: https://wpartisan.me
 * Version: 1.0.0
 * Plugin URI: https://emailsummarypro.com
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Email_Summary_Pro' ) ) :

	/**
	 * Main Email_Summary_Pro Class
	 *
	 * Code layout largly copied from Affiliate_WP
	 *
	 * @since 1.0
	 */
	final class Email_Summary_Pro {
		/** Singleton *************************************************************/

		/**
		 * Email_Summary_Pro instance.
		 *
		 * @access private
		 * @since  1.0
		 * @var    Email_Summary_Pro The one true Email_Summary_Pro
		 */
		private static $instance;

		/**
		 * The version number of Email_Summary_Pro.
		 *
		 * @access private
		 * @since  1.0
		 * @var    string
		 */
		private $version = '1.0.0';

		/**
		 * The tabs help class.
		 *
		 * @access public
		 * @since  1.0
		 * @var    Email_Summary_Pro_Tabs_Helper
		 */
		public $tabs_helper;

		/**
		 * The main admin class.
		 *
		 * @access public
		 * @since  1.0
		 * @var    Email_Summary_Pro_Admin
		 */
		public $admin;

		/**
		 * The admin debug class.
		 *
		 * @access public
		 * @since  1.0
		 * @var    Email_Summary_Pro_Admin_Debug
		 */
		public $admin_debug;

		/**
		 * The admin debug class.
		 *
		 * @access public
		 * @since  1.0
		 * @var    Email_Summary_Pro_Admin_Extensions
		 */
		public $admin_extensions;

		/**
		 * Main Email_Summary_Pro Instance
		 *
		 * Insures that only one instance of Email_Summary_Pro exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @return Email_Summary_Pro
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Email_Summary_Pro ) ) {
				self::$instance = new Email_Summary_Pro;

				self::$instance->setup_constants();
				self::$instance->includes();

				add_action( 'plugins_loaded', array( self::$instance, 'setup_objects' ), -1 );
				// add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
			}
			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'email-summary-pro' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'email-summary-pro' ), '1.0' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'ESP_VERSION' ) ) {
				define( 'ESP_VERSION', $this->version );
			}

			// Plugin base path.
			if ( ! defined( 'ESP_BASE_PATH' ) ) {
				define( 'ESP_BASE_PATH', plugin_dir_path( __FILE__ ) );
			}

			// Plugin base URL.
			if ( ! defined( 'ESP_BASE_URL' ) ) {
				define( 'ESP_BASE_URL', plugin_dir_url( __FILE__ ) );
			}

		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {
			require ESP_BASE_PATH . 'includes/post-types.php';
			require ESP_BASE_PATH . 'includes/functions-helper.php';
			require ESP_BASE_PATH . 'includes/functions-stats.php';
			require ESP_BASE_PATH . 'includes/functions-summaries.php';
			require ESP_BASE_PATH . 'includes/actions-cron.php';
			require ESP_BASE_PATH . 'includes/class-tabs-helper.php';
			require ESP_BASE_PATH . 'includes/class-email.php';
			require ESP_BASE_PATH . 'includes/class-summary.php';
			require ESP_BASE_PATH . 'includes/admin/summaries/class-admin-summaries-list-table.php';
			require ESP_BASE_PATH . 'includes/admin/admin-actions.php';
			require ESP_BASE_PATH . 'includes/admin/admin-notices.php';
			require ESP_BASE_PATH . 'includes/admin/class-admin-base.php';
			require ESP_BASE_PATH . 'includes/admin/class-admin.php';
			require ESP_BASE_PATH . 'includes/admin/class-admin-debug.php';
			require ESP_BASE_PATH . 'includes/admin/class-admin-extensions.php';
		}

		/**
		 * Setup all objects
		 *
		 * @access public
		 * @since 1.6.2
		 * @return void
		 */
		public function setup_objects() {
			self::$instance->tabs_helper      = new Email_Summary_Pro_Tabs_Helper;
			self::$instance->admin            = new Email_Summary_Pro_Admin;
			self::$instance->admin_debug      = new Email_Summary_Pro_Admin_Debug;
			self::$instance->admin_extensions = new Email_Summary_Pro_Admin_Extensions;
		}

	}

endif;

/**
 * The main function responsible for returning the one true Email_Summary_Pro
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $esp = email_summary_pro(); ?>
 *
 * @since 1.0.0
 * @return Email_Summary_Pro The one true Email_Summary_Pro Instance
 */
function email_summary_pro() {
	return Email_Summary_Pro::instance();
}
email_summary_pro();
