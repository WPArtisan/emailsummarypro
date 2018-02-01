<?php
/**
 * Admin class.
 *
 * @since 1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

 /**
  * Main admin class for the plugin.
  *
  * Sets up all menus, settings, pages and dashboards.
  *
  * @since  1.0.0
  */
class Email_Summary_Pro_Admin extends Email_Summary_Pro_Admin_Base {

	/**
	 * An instance of the Helper_Tabs class.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var WP_Roundup_Helper_Tabs
	 */
	public $tabs;

	/**
	 * The slug of the current page.
	 *
	 * Used for registering menu items and tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $page_slug = 'wp_roundup';


	/**
	 * Hooks registered in this class.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function hooks() {
		// add_action( 'admin_init', array( $this, 'setup_settings' ), 10, 0 );
		// add_action( 'admin_init', array( $this, 'resend_roundup' ), 10, 0 );
		add_action( 'admin_menu', array( $this, 'add_menu_items' ), 23, 0 );
	}

	/**
	 * Setup menu items.
	 *
	 * This adds a menu page to the WordPress Settings menu
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function add_menu_items() {

		$page_slug = add_submenu_page(
			'options-general.php',
			esc_html__( 'Email Summary Pro', 'email-summary-pro' ),
			esc_html__( 'Email Summary Pro', 'email-summary-pro' ),
			'manage_options',
			'email_summary_pro',
			array( $this, 'output_callback' )
		);

		// add_action( 'load-' . $page_hook, array( $this, 'setup_tabs' ) );
		// add_action( 'load-' . $page_hook, array( $this, 'setup_meta_boxes' ) );
	}

	/**
	 * Outputs HTML for the settings page.
	 *
	 * The Facebook settings page is a tabbed interface. It uses
	 * the WPNA_Helper_Tabs class to setup and register the tabbed interface.
	 * The WPNA_Helper_Tabs class is initiated in the setup_tabs method.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return nul
	 */
	public function output_callback() {
		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"></div>
			<!-- <h1><?php esc_html_e( 'WP Roundup', 'email-summary-pro' ); ?></h1> -->
			<div class="wrap">
				<?php // $this->tabs->tabs_nav(); ?>
				<?php // $this->tabs->tabs_content(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Sets up the tab helper for the Admin Facebook page.
	 *
	 * Creates a new instance of the WP_Roundup_Helper_Tabs class and registers the
	 * first tab, 'Settings'. Other tabs are added using the
	 * 'wp_roundup_admin_tabs' action.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function setup_tabs() {
		$this->tabs = new WP_Roundup_Helper_Tabs();

		$this->tabs->register_tab(
			'Settings',
			esc_html__( 'Settings', 'email-summary-pro' ),
			$this->page_url(),
			array( $this, 'settings_tab_callback' ),
			true
		);

		/**
		 * Called after the first tab has been setup for this page.
		 * Passes the tabs in so it can be modified, other tabs added etc.
		 *
		 * @since 1.0.0
		 * @param WP_Roundup_Helper_Tabs $this->tabs Instance of the tabs helper. Used
		 *                                           to register new tabs.
		 */
		do_action( 'wp_roundup_admin_tabs', $this->tabs );
	}

	/**
	 * Setup the screen columns.
	 *
	 * Do actions for registering meta boxes for this screen.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function setup_meta_boxes() {
		$screen = get_current_screen();

		/**
		 * Trigger the add_meta_boxes_{$screen_id} hook to allow meta boxes
		 * to be added to this screen.
		 *
		 * @since 1.0.0
		 */
		do_action( 'add_meta_boxes_' . $screen->id );

		/**
		* Trigger the add_meta_boxes hook to allow meta boxes to be added.
		 *
		 * @since 1.0.0
		 * @param string $screen->id The ID of the screen for the admin page.
		 */
		do_action( 'add_meta_boxes', $screen->id );

		// Add screen option: user can choose between 1 or 2 columns (default 2)
		add_screen_option( 'layout_columns', array( 'max' => 2, 'default' => 2 ) );
	}

	/**
	 * Register general Facebook settings.
	 *
	 * Uses the settings API to create and register all the settings fields in
	 * the General tab of the Facebook admin. Uses the global wpna_sanitize_options()
	 * function to provide validation hooks based on each field name.
	 *
	 * The settings API replaces the entire global settings object with the new
	 * values. wpna_sanitize_options() takes any other fields found in the global
	 * settings array that aren't registered here and merges them in to ensure
	 * they're not lost.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function setup_settings() {

		// HTML Email

		// Patch WP Mailer

		register_setting( 'wp_roundup-settings', 'wp_roundup_options', array( $this, 'wp_roundup_sanitize_options' ) );

		add_settings_section(
			'wp_roundup-settings',
			esc_html__( 'Settings', 'wp-native-articles' ),
			array( $this, 'settings_section_callback' ),
			$this->page_slug
		);

		add_settings_field(
			'wp_roundup_html_emails',
			'<label for="html_emails">' . esc_html__( 'HTML Emails', 'wp-native-articles' ) . '</label>',
			array( $this, 'html_emails_field_callback' ),
			$this->page_slug,
			'wp_roundup-settings'
		);

		add_settings_field(
			'wp_roundup_recipients',
			'<label for="recipients">' . esc_html__( 'Recipients', 'wp-native-articles' ) . '</label>',
			array( $this, 'recipients_field_callback' ),
			$this->page_slug,
			'wp_roundup-settings'
		);

		add_settings_field(
			'resend_roundup',
			'<label for="">' . esc_html__( 'Resend Roundup', 'wp-native-articles' ) . '</label>',
			array( $this, 'resend_roundup_field_callback' ),
			$this->page_slug,
			'wp_roundup-settings'
		);
	}

	/**
	 * Output the HTML for the Settings tab.
	 *
	 * Uses the settings API and outputs the fields registered.
	 * settings_fields() requires the name of the group of settings to ouput.
	 * do_settings_sections() requires the unique page slug for this settings form.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function settings_tab_callback() {
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'wp_roundup-settings' ); ?>
			<?php do_settings_sections( $this->page_slug ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Outputs the HTML displayed at the top of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function settings_section_callback() {
		?>
		<?php
	}

	/**
	 * Outputs the HTML for the 'html_emails' settings field.
	 *
	 * Whether to use HTML emails or not.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function html_emails_field_callback() {
		?>
		<label for="html_emails">
			<input type="hidden" name="wp_roundup_options[html_emails]" value="0">
			<input type="checkbox" name="wp_roundup_options[html_emails]" id="html_emails" class="" value="true"<?php checked( (bool) wp_roundup_get_option('html_emails') ); ?> />
			<?php esc_html_e( 'Uncheck this for older email clients.', 'email-summary-pro' ); ?>
		</label>
		<?php
	}

	/**
	* Outputs the HTML for the 'recipient' settings field.
	*
	* Multi recipients can be added with commas.
	*
	* @since 1.0.0
	*
	* @access public
	* @return null
	*/
	public function recipients_field_callback() {
		?>
		<input type="text" name="wp_roundup_options[recipients]" id="recipients" class="regular-text" value="<?php echo esc_attr( wp_roundup_get_option('recipients') ); ?>">
		<p class="description"><?php _e( 'Multiple recipients can be added using commas. e.g. <code>admin1@site.com, admin2@site.com</code>', 'email-summary-pro' ); ?></p>
		<?php
	}

	/**
	* Outputs the HTML for the resend_last_roundup button
	*
	* Just a link back to the current page with a variable set.
	*
	* @since 1.0.0
	* @todo Move to ajax?
	*
	* @access public
	* @return null
	*/
	public function resend_roundup_field_callback() {
		// Get the current page URL
		$url = menu_page_url( $this->page_slug, false );

		// Add these params
		$query = array(
			'tab'    => 'settings',
			'action' => 'resend_roundup',
		);

		// Reconstruct the URL
		$url = add_query_arg( $query, $url );

		?>
		<a href="<?php echo esc_url( $url ); ?>" class="buton button-secondary"><?php esc_html_e( 'Resend', 'email-summary-pro' ); ?></a>
		<?php
	}

	/**
	 * Resends the last stats email.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function resend_roundup() {
		if ( empty( $_GET['action'] ) || 'resend_roundup' != $_GET['action'] )
			return;

		if ( ! current_user_can('manage_options') )
			return;

		$roundup = new WP_Roundup();
		$roundup->date( '2016-04-12' );
		$roundup->send();

	}

}
