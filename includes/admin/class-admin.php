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
	 * Holds an instance of the List Table.
	 *
	 * @var WPNA_Admin_Summaries_List_Table
	 */
	public $summaries_list_table;

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
		add_action( 'admin_menu',          array( $this, 'add_menu_items' ), 23 );
		add_action( 'esp_resend_summary',  array( $this, 'resend_summary' ), 10 );
		add_action( 'esp_preview_summary', array( $this, 'preview_summary' ), 10 );
		add_action( 'esp_add_summary',     array( $this, 'add_summary_action' ), 10, 1 );
		add_action( 'esp_edit_summary',    array( $this, 'edit_summary_action' ), 10, 1 );
		add_filter( 'set-screen-option',   array( $this, 'set_screen_option' ), 10, 3 );
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

		add_action( "load-{$menu_id}", array( $this, 'page_hooks' ), 1, 0 );

		/**
		 * Custom action for adding more menu items.
		 *
		 * @since 1.0.0
		 * @param string $menu_id The unique ID for the menu page.
		 * @param string $page_slug The unique slug for the menu page.
		 */
		do_action( 'esp_admin_menu_items', $menu_id, $this->page_slug );
	}

	/**
	 * Add page specific hooks based of the page screen id.
	 *
	 * The advantage of this is we never have to hard code the $page_hook anywhere.
	 *
	 * @access public
	 * @return void
	 */
	public function page_hooks() {
		// @todo Add better help.
		if ( 1 === 2 ) {
			// add_action( current_action(), 'esp_contextual_help', 10, 0 );
		}

		add_action( current_action(), array( $this, 'setup_tabs' ), 10 );
		add_action( current_action(), array( $this, 'add_screen_options' ), 10, 0 );
		add_action( current_action(), array( $this, 'setup_admin_summaries_list_table' ), 10, 0 );
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 1 );
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
			array( $this, 'list_table_output_callback' ),
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
	 * Setup screen options for the summaries table.
	 *
	 * @access public
	 * @return void
	 */
	public function add_screen_options() {

		$args = array(
			'label'   => esc_html__( 'Summaries', 'email-summary-pro' ),
			'default' => 20,
			'option'  => 'summaries_per_page',
		);

		add_screen_option( 'per_page', $args );
	}

	/**
	 * Sets the value for the screen option when retrieved.
	 *
	 * @param string $status Screen option.
	 * @param string $option Name of the screen option to get the value for.
	 * @param int    $value Stored value for the screen option.
	 * @return int The value to set.
	 */
	public function set_screen_option( $status, $option, $value ) {

		if ( 'summaries_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Setup the WPNA_Admin_Summaries_List_Table & process bulk actions.
	 *
	 * The same instance needs to be used in the screen options and to
	 * display the table.
	 *
	 * Process bulk action contains redirects so needs to run early on.
	 *
	 * @return void
	 */
	public function setup_admin_summaries_list_table() {
		// Setup the class instance.
		$this->summaries_list_table = new WPNA_Admin_Summaries_List_Table();
		// Check for bulk action and process.
		$this->summaries_list_table->process_bulk_action();
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
		// @codingStandardsIgnoreLine
		if ( isset( $_GET['esp-action'] ) && 'add_summary' === $_GET['esp-action'] ) {
			// Load the summary template.
			require ESP_BASE_PATH . '/includes/admin/summaries/add-summary.php';
		// @codingStandardsIgnoreLine
		} elseif ( isset( $_GET['esp-action'] ) && 'edit_summary' === $_GET['esp-action'] ) {
			// Load the edit summary template.
			require ESP_BASE_PATH . '/includes/admin/summaries/edit-summary.php';
		} else {
			$this->tab_callback();
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
	 * Outputs HTML for Summaries page.
	 *
	 * @access public
	 * @return void
	 */
	public function list_table_output_callback() {
		?>
		<div class="wrap">
			<h1>
				<?php esc_html_e( 'Summmaries', 'email-summary-pro' ); ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'esp-action' => 'add_summary' ), admin_url( 'options-general.php?page=email_summary_pro' ) ) ); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'email-summary-pro' ); ?></a>
			</h1>
			<h4>
				<p><?php esc_html_e( 'Email Summaries are a round up of what has been happening on your site.', 'email-summary-pro' ); ?></p>
			</h4>

			<?php do_action( 'esp_summaries_page_top' ); ?>

			<form id="esp-summaries-list-table" action="<?php echo esc_url( admin_url( 'options-general.php?page=email_summary_pro' ) ); ?>" method="post">
				<input type="hidden" name="page" value="esp_summaries" />
			<?php
				$this->summaries_list_table->prepare_items();
				$this->summaries_list_table->search_box( esc_html__( 'Search Summaries', 'email-summary-pro' ), 'search_id' );
				$this->summaries_list_table->display();
			?>
			</form>

			<?php do_action( 'esp_summaries_page_bottom' ); ?>

		</div>
		<?php
	}

	/**
	 * Fired when the add summary action is triggered.
	 *
	 * Validates and saves a summary.
	 *
	 * @param array $data Raw, unfiltered POST data.
	 * @return void
	 */
	public function add_summary_action( $data ) {

		// Only proceed if the form has been submitted.
		if ( ! isset( $data['submit'] ) ) {
			return;
		}

		if ( ! isset( $data['esp-summary-nonce'] ) || ! wp_verify_nonce( $data['esp-summary-nonce'], 'esp_summary_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to create summaries', 'email-summary-pro' ), esc_html__( 'Error', 'email-summary-pro' ), array( 'response' => 403 ) );
		}

		// Try and insert the summary.
		if ( esp_add_summary( $data ) ) {
			wp_safe_redirect( add_query_arg( 'esp-notice', 'summary_added_success' ) );
			die;
		} else {
			wp_safe_redirect( add_query_arg( 'esp-noitce', 'summary_added_error' ) );
			die;
		}

	}

	/**
	 * Fired when the edit summary action is triggered.
	 *
	 * Validates and saves a summary.
	 *
	 * @param array $data Raw, unfiltered POST data.
	 * @return void
	 */
	public function edit_summary_action( $data ) {

		// Only proceed if the form has been submitted.
		if ( ! isset( $data['submit'] ) ) {
			return;
		}

		if ( ! isset( $data['esp-summary-nonce'] ) || ! wp_verify_nonce( $data['esp-summary-nonce'], 'esp_summary_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage summaries', 'email-summary-pro' ), esc_html__( 'Error', 'email-summary-pro' ), array( 'response' => 403 ) );
		}

		if ( empty( $data['summary_id'] ) ) {
			wp_safe_redirect( add_query_arg( 'esp-notice', 'summary_validation_fail' ) );
			die;
		}

		// Try and insert the summary.
		if ( esp_update_summary( $data['summary_id'], $data ) ) {
			wp_safe_redirect( add_query_arg( 'esp-notice', 'summary_update_success' ) );
			die;
		} else {
			wp_safe_redirect( add_query_arg( 'esp-notice', 'summary_update_error' ) );
			die;
		}

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
