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
	 * The slug of the current page.
	 *
	 * Used for registering menu items and tabs.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $page_slug;

	/**
	 * An instance of the Email_Summary_Pro_Helper_Tabs class.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Email_Summary_Pro_Helper_Tabs
	 */
	public $tabs;

	/**
	 * Hooks registered in this class.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return null
	 */
	public function hooks() {
		add_action( 'admin_menu',         array( $this, 'add_menu_items' ), 23 );
		add_action( 'esp_resend_summary', array( $this, 'resend_summary' ), 10 );
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

		$this->page_slug = add_submenu_page(
			'options-general.php',
			esc_html__( 'Email Summary Pro', 'email-summary-pro' ),
			esc_html__( 'Email Summary Pro', 'email-summary-pro' ),
			'manage_options',
			'email_summary_pro',
			array( $this, 'output_callback' )
		);

		add_action( 'load-' . $this->page_slug, array( $this, 'setup_tabs' ), 10 );
		add_action( 'load-' . $this->page_slug, array( $this, 'setup_settings' ), 10 );
	}

	/**
	 * Outputs HTML for the settings page.
	 *
	 * The menu page is a tabbed interface. It uses
	 * the Email_Summary_Pro_Tabs_Helper class to register the tabbed interface.
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
			<h1><?php esc_html_e( 'WP Roundup', 'email-summary-pro' ); ?></h1>
			<div class="wrap">
				<?php email_summary_pro()->tabs_helper->tabs_nav(); ?>
				<?php email_summary_pro()->tabs_helper->tabs_content(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Sets up the tab helper for the Admin Facebook page.
	 *
	 * @access public
	 * @return null
	 */
	public function setup_tabs() {
		email_summary_pro()->tabs_helper->register_tab(
			'settings',
			esc_html__( 'Settings', 'email-summary-pro' ),
			$this->page_url(),
			array( $this, 'settings_tab_callback' ),
			true
		);
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

		$setting_name = 'esp_general_settings';

		register_setting( $setting_name, 'esp_options', 'esp_sanitize_options' );

		add_settings_section(
			$setting_name,
			esc_html__( 'Settings', 'email-summary-pro' ),
			array( $this, 'settings_section_callback' ),
			$setting_name
		);

		add_settings_field(
			'esp_html_emails',
			'<label for="html_emails">' . esc_html__( 'HTML Emails', 'email-summary-pro' ) . '</label>',
			array( $this, 'html_emails_field_callback' ),
			$setting_name,
			$setting_name
		);

		add_settings_field(
			'esp_roundup_recipients',
			'<label for="recipients">' . esc_html__( 'Recipients', 'email-summary-pro' ) . '</label>',
			array( $this, 'recipients_field_callback' ),
			$setting_name,
			$setting_name
		);

		add_settings_field(
			'esp_resend_summary',
			'<label for="">' . esc_html__( 'Resend Roundup', 'email-summary-pro' ) . '</label>',
			array( $this, 'resend_summary_field_callback' ),
			$setting_name,
			$setting_name
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
			<?php settings_fields( 'esp_general_settings' ); ?>
			<?php do_settings_sections( 'esp_general_settings' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Outputs the HTML displayed at the top of the settings section.
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
	 * @access public
	 * @return null
	 */
	public function html_emails_field_callback() {
		?>
		<label for="html-emails">
			<input type="hidden" name="esp_options[html_emails]" value="0">
			<input type="checkbox" name="esp_options[html_emails]" id="html-emails" class="" value="true"<?php checked( (bool) esp_get_option( 'html_emails' ) ); ?> />
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
		<input type="text" name="esp_options[recipients]" id="recipients" class="regular-text" value="<?php echo esc_attr( esp_get_option( 'recipients' ) ); ?>">
		<p class="description"><?php esc_html_e( 'Multiple recipients can be added using commas. e.g. <code>admin1@site.com, admin2@site.com</code>', 'email-summary-pro' ); ?></p>
		<?php
	}

	/**
	* Outputs the HTML for the resend_last_roundup button
	*
	* Just a link back to the current page with an action set.
	*
	* @access public
	* @return null
	*/
	public function resend_summary_field_callback() {
		// Add these params.
		$query = array(
			'page'       => 'email_summary_pro',
			'tab'        => 'settings',
			'esp-action' => 'resend_summary',
		);

		// Reconstruct the URL
		$url = add_query_arg( $query, admin_url( 'options-general.php' ) );
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
	public function resend_summary() {
		// Check it's an admin user.
		if ( ! current_user_can('manage_options') ) {
			return;
		}

		// Check the nonce.

		// $roundup = new WP_Roundup();
		// $roundup->date( '2016-04-12' );
		// $roundup->send();

		// Add these params.
		$query = array(
			'page'       => 'email_summary_pro',
			'tab'        => 'settings',
			'esp-notice' => 'resend_summary_success',
		);

		// Reconstruct the URL
		$url = add_query_arg( $query, admin_url( 'options-general.php' ) );

		wp_safe_redirect( $url );
		exit;
	}

}
