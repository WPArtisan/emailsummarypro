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
		add_action( 'admin_init',          array( $this, 'setup_settings' ), 10 );
		add_action( 'admin_menu',          array( $this, 'add_menu_items' ), 23 );
		add_action( 'esp_resend_summary',  array( $this, 'resend_summary' ), 10 );
		add_action( 'esp_preview_summary', array( $this, 'preview_summary' ), 10 );
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
		<div class="wrap email-summary-pro">
			<div id="icon-tools" class="icon32"></div>
			<h1><?php esc_html_e( 'Email Summary Pro', 'email-summary-pro' ); ?></h1>
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
			'html_emails',
			'<label for="html_emails">' . esc_html__( 'HTML Emails', 'email-summary-pro' ) . '</label>',
			array( $this, 'html_emails_field_callback' ),
			$setting_name,
			$setting_name
		);

		add_settings_field(
			'recipients',
			'<label for="recipients">' . esc_html__( 'Recipients', 'email-summary-pro' ) . '</label>',
			array( $this, 'recipients_field_callback' ),
			$setting_name,
			$setting_name
		);

		add_settings_field(
			'resend_summary',
			'<label for="">' . esc_html__( 'Resend Summary', 'email-summary-pro' ) . '</label>',
			array( $this, 'resend_summary_field_callback' ),
			$setting_name,
			$setting_name
		);

		add_settings_field(
			'scheduled',
			'<label for="">' . esc_html__( 'Scheduled', 'email-summary-pro' ) . '</label>',
			array( $this, 'scheduled_field_callback' ),
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
		<p class="description"><?php echo sprintf( esc_html__( 'Multiple recipients can be added using commas. e.g. %s', 'email-summary-pro'), '<code>admin1@site.com, admin2@site.com</code>' ); ?></p>
		<?php
	}

	/**
	* Outputs the HTML for the resend_last_roundup button
	*
	* Just a link back to the current page with an action set.
	*
	* @access public
	* @return void
	*/
	public function resend_summary_field_callback() {
		// Work out the date of the last summary.
		$start_of_week = get_option( 'start_of_week' );
		$start_of_week_day = date( 'l', strtotime( "Sunday + {$start_of_week} Days" ) );
		$last_summary = date( 'Y-m-d', strtotime( 'last ' . $start_of_week_day ) );

		// Add these params.
		$default_query = array(
			'page' => 'email_summary_pro',
		);

		// Resend URL.
		$resend_url = wp_nonce_url( add_query_arg( array_merge( $default_query, array( 'esp-action' => 'resend_summary' ) ), admin_url( 'options-general.php' ) ), 'resend_summary', 'esp_nonce');

		// Preview URL.
		$preview_url = wp_nonce_url( add_query_arg( array_merge( $default_query, array( 'esp-action' => 'preview_summary' ) ), admin_url( 'options-general.php' ) ), 'preview_summary', 'esp_nonce');
		?>
		<input type="date" id="summary-week" max="<?php echo date( 'Y-m-d' ); ?>" value="<?php echo esc_attr( $last_summary ); ?>" >
		<a href="#" data-url="<?php echo esc_url( $resend_url ); ?>" class="button button-secondary js-url-action" title="<?php esc_html_e( 'Resend Email Summary', 'email-summary-pro' ); ?>"><?php esc_html_e( 'Resend', 'email-summary-pro' ); ?></a>
		<a href="#" data-url="<?php echo esc_url( $preview_url ); ?>" class="button button-secondary js-url-action" title="<?php esc_html_e( 'Preview Email Summary', 'email-summary-pro' ); ?>" target="_blank" ><?php esc_html_e( 'Preview', 'email-summary-pro' ); ?></a>
		<p class="description"><?php esc_html_e( 'Select a past date to resend, or preview, the email summary for that week.', 'easy-summary-pro' ); ?></p>
		<script>
			var setUrl = function( ev ) {
				var el = ev.target;
				// Get the date to summerise.
				var summaryWeek = document.querySelector( 'input#summary-week' ).value;
				// Get the URL.
				var url = el.getAttribute( 'data-url' );
				// Replace and continue.
				el.setAttribute( 'href', url + '&date=' + summaryWeek );
			};

			// Set watchers for every URL action.
			[].forEach.call( document.querySelectorAll( '.js-url-action' ), function(el) {
				el.addEventListener( 'click', setUrl.bind() );
			} );
		</script>
		<?php
	}

	/**
	* Outputs the HTML for the scheduled field.
	*
	* Works out the tme the next summary email is scheduled for.
	*
	* @access public
	* @return void
	*/
	public function scheduled_field_callback() {
		?>
		<p class="description"><i>01:00, Firday 20th 2018</i></p>
		<?php
	}

	/**
	 * Resends the last stats email.
	 *
	 * @return null
	 */
	public function resend_summary() {
		// Check it's an admin user.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check the nonce.
		if ( ! isset( $_GET['esp_nonce'] ) || ! wp_verify_nonce( $_GET['esp_nonce'], 'resend_summary' ) ) {
			wp_die( __( 'Cheatin&#8217; huh?', 'email-summary-pro' ) );
		}

		// Setup new email summary and send it.
		$summary = new Email_Summary_Pro_Email();

		if ( ! empty( $_GET['date'] ) ) {
			$summary->date( $_GET['date'] );
		}

		$summary->send();

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

	/**
	 * Resends the last stats email.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function preview_summary() {
		// Check it's an admin user.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check the nonce.
		if ( ! isset( $_GET['esp_nonce'] ) || ! wp_verify_nonce( $_GET['esp_nonce'], 'preview_summary' ) ) {
			wp_die( __( 'Cheatin&#8217; huh?', 'email-summary-pro' ) );
		}

		// Setup new email summary and output the preview.
		$summary = new Email_Summary_Pro_Email();

		if ( ! empty( $_GET['date'] ) ) {
			$summary->date( $_GET['date'] );
		}

		echo $summary->get_template( 'html' );
		die;
	}

}
