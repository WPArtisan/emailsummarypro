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
	public $page_slug = 'email_summary_pro';

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
	 * @return void
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
	 * @return void
	 */
	public function add_menu_items() {
		$menu_id = add_submenu_page(
			'options-general.php',
			esc_html__( 'Email Summary Pro', 'email-summary-pro' ),
			esc_html__( 'Email Summary Pro', 'email-summary-pro' ),
			'manage_options',
			'email_summary_pro',
			array( $this, 'output_callback' )
		);

		add_action( 'load-' . $menu_id, array( $this, 'setup_tabs' ), 10 );
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
	 * @return void
	 */
	public function setup_tabs() {
		email_summary_pro()->tabs_helper->register_tab(
			'settings',
			esc_html__( 'Summary', 'email-summary-pro' ),
			$this->page_url(),
			array( $this, 'tab_callback' ),
			true
		);

		/**
		 * Use to add more tabs to the admin.
		 *
		 * @var string Page URL for the tabs.
		 */
		do_action( 'esp_admin_tabs', $this->page_url() );
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
	 * @return void
	 */
	public function setup_settings() {

		// Unique key for the settings API on this page.
		$option_group = 'esp_settings';

		// Register the setting.
		register_setting( $option_group, 'esp_options', 'esp_sanitize_options' );

		// Setup the general settings section.
		add_settings_section(
			$option_group,
			esc_html__( 'General', 'email-summary-pro' ),
			array( $this, 'settings_section_callback' ),
			$option_group
		);

		// Register the default fields.
		$settings_fields = array(
			array(
				'key'      => 'recipients',
				'label'    => esc_html__( 'Recipients', 'email-summary-pro' ),
				'callback' => array( $this, 'recipients_field_callback' ),
				'order'    => 10,
			),
			array(
				'key'      => 'disable_html_emails',
				'label'    => esc_html__( 'Disable HTML Emails', 'email-summary-pro' ),
				'callback' => array( $this, 'disable_html_emails_field_callback' ),
				'order'    => 20,
			),
			array(
				'key'      => 'resend_summary',
				'label'    => esc_html__( 'Resend Summary', 'email-summary-pro' ),
				'callback' => array( $this, 'resend_summary_field_callback' ),
				'order'    => 30,
			),
			array(
				'key'      => 'next_summary',
				'label'    => esc_html__( 'Next Summary', 'email-summary-pro' ),
				'callback' => array( $this, 'next_summary_field_callback' ),
				'order'    => 40,
			),
		);

		/**
		 * Filter the fields for the this settings section.
		 *
		 * Use this filter to add any more fields in, or change the order.
		 *
		 * @var array
		 */
		$settings_fields = apply_filters( 'esp_settings_fields_general', $settings_fields, $option_group );

		// Order the fields.
		usort( $settings_fields, 'esp_sort_by_order' );

		// Setup all the registered fields.
		foreach ( $settings_fields as $field ) {
			add_settings_field(
				$field['key'],
				'<label for="' . esc_attr( $field['key'] ) . '">' . esc_html( $field['label'] ) . '</label>',
				$field['callback'],
				$option_group,
				$option_group
			);
		}

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
	 * @return void
	 */
	public function tab_callback() {
		?>
		<form action="options.php" method="post">
			<?php settings_fields( 'esp_settings' ); ?>
			<?php do_settings_sections( 'esp_settings' ); ?>
			<?php submit_button(); ?>
		</form>
		<?php
	}

	/**
	 * Outputs the HTML displayed at the top of the settings section.
	 *
	 * @access public
	 * @return void
	 */
	public function settings_section_callback() {
		?>
		<p><?php esc_html_e( 'Email Summaries are a round up of what has been happening on your site.', 'email-summary-pro' ); ?></p>
		<?php
	}

	/**
	 * Outputs the HTML for the 'recipient' settings field.
	 *
	 * Multi recipients can be added with commas.
	 *
	 * @access public
	 * @return void
	 */
	public function recipients_field_callback() {
		?>
		<input type="text" name="esp_options[recipients]" id="recipients" class="regular-text" value="<?php echo esc_attr( esp_get_option( 'recipients' ) ); ?>">
		<p class="description"><?php echo sprintf( esc_html__( 'Multiple recipients can be added using commas. e.g. %s', 'email-summary-pro'), '<code>admin1@site.com, admin2@site.com</code>' ); ?></p>
		<?php
	}

	/**
	 * Outputs the HTML for the 'html_emails' settings field.
	 *
	 * Whether to use HTML emails or not.
	 *
	 * @access public
	 * @return void
	 */
	public function disable_html_emails_field_callback() {
		?>
		<label for="disable-html-emails">
			<input type="hidden" name="esp_options[disable_html_emails]" value="0">
			<input type="checkbox" name="esp_options[disable_html_emails]" id="disable_html_emails" class="" value="true"<?php checked( (bool) esp_get_option( 'disable_html_emails' ) ); ?> />
			<?php esc_html_e( 'Disable HTML emails and only recieve plain text ones.', 'email-summary-pro' ); ?>
		</label>
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
		$preview_url = add_query_arg( array_merge( $default_query, array( 'esp-action' => 'preview_summary' ) ), admin_url( 'options-general.php' ) );
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
	public function next_summary_field_callback() {
		$next = wp_next_scheduled( 'esp_cron_hook' );
		?>
		<p class="description"><code><?php echo esc_html( date( 'H:ma, jS M Y', $next ) ); ?></code></p>
		<?php
	}

	/**
	 * Resends the last stats email.
	 *
	 * @return void
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
	 * @return void
	 */
	public function preview_summary() {
		// Check it's an admin user.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
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
