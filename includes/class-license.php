<?php
/**
 * A license helper class.
 *
 * Extensions can invoke this class to set themselves up with a license.
 * This is heavily borrowed from EDD.
 *
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Email_Summary_Pro_License' ) ) :

	/**
	 *
	 * @since 1.0.0
	 */
	class Email_Summary_Pro_License {

		public $file;
		public $item_name;
		public $item_shortname;
		public $item_id;
		public $version;
		public $author;
		public $api_url = 'https://emailsummarypro.com/';

		public function __construct( $file, $item_id, $item_name, $version, $author ) {
			$this->file           = $file;
			$this->item_id        = absint( $item_id );
			$this->item_name      = $item_name;
			$this->item_shortname = 'esp_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
			$this->version        = $version;
			$this->license        = trim( esp_get_option( $this->item_shortname . '_license_key', '' ) );
			$this->author         = $author;

			$this->includes();
			$this->hooks();
		}

		/**
		 * Include the EDD plugin updater class.
		 *
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			if ( ! class_exists( 'ESP_SL_Plugin_Updater' ) )  {
				require_once 'ESP_SL_Plugin_Updater.php';
			}
		}

		/**
		 * Hooks registered in this class.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 * @return void
		 */
		public function hooks() {

			// Register settings
			add_filter( 'esp_settings_licenses', array( $this, 'settings' ), 1 );

			// Display help text at the top of the Licenses tab
			add_action( 'esp_settings_tab_top', array( $this, 'license_help_text' ) );

			// Activate license key on settings save
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			// Check that license is valid once per week
			if ( esp_doing_cron() ) {
				add_action( 'esp_weekly_scheduled_events', array( $this, 'weekly_license_check' ) );
			}

			// For testing license notices, uncomment this line to force checks on every page load
			//add_action( 'admin_init', array( $this, 'weekly_license_check' ) );

			// Updater
			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			// Display notices to admins
			add_action( 'admin_notices', array( $this, 'notices' ) );

			add_action( 'in_plugin_update_message-' . plugin_basename( $this->file ), array( $this, 'plugin_row_license_missing' ), 10, 2 );

			// Register plugins for beta support
			add_filter( 'esp_beta_enabled_extensions', array( $this, 'register_beta_support' ) );
		}

		/**
		 * Auto updater
		 *
		 * @access  private
		 * @return  void
		 */
		public function auto_updater() {
			$betas = esp_get_option( 'enabled_betas', array() );

			$args = array(
				'version'   => $this->version,
				'license'   => $this->license,
				'author'    => $this->author,
				'beta'      => function_exists( 'esp_extension_has_beta_support' ) && esp_extension_has_beta_support( $this->item_shortname ),
			);

			if( ! empty( $this->item_id ) ) {
				$args['item_id']   = $this->item_id;
			} else {
				$args['item_name'] = $this->item_name;
			}

			// Setup the updater
			$esp_updater = new ESP_SL_Plugin_Updater(
				$this->api_url,
				$this->file,
				$args
			);
		}

		/**
		 * Add license field to settings
		 *
		 * @param array   $settings
		 * @return  array
		 */
		public function settings( $settings ) {
			$esp_license_settings = array(
				array(
					'id'      => $this->item_shortname . '_license_key',
					'name'    => sprintf( __( '%1$s', 'email-summary-pro' ), $this->item_name ),
					'desc'    => '',
					'type'    => 'license_key',
					'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
					'size'    => 'regular'
				)
			);

			return array_merge( $settings, $esp_license_settings );
		}

		/**
		 * Display help text at the top of the Licenses tag
		 *
		 * @since   2.5
		 * @param   string   $active_tab
		 * @return  void
		 */
		public function license_help_text( $active_tab = '' ) {

			static $has_ran;

			if( 'licenses' !== $active_tab ) {
				return;
			}

			if( ! empty( $has_ran ) ) {
				return;
			}

			echo '<p>' . sprintf(
				__( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please <a href="%s" target="_blank">renew your license</a>.', 'email-summary-pro' ),
				'http://docs.emailsummarypro.com/article/1000-license-renewal'
			) . '</p>';

			$has_ran = true;

		}

		/**
		 * Activate the license key
		 *
		 * @return  void
		 */
		public function activate_license() {

			if ( ! isset( $_POST['esp_settings'] ) ) {
				return;
			}

			if ( ! isset( $_REQUEST[ $this->item_shortname . '_license_key-nonce'] ) || ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce'], $this->item_shortname . '_license_key-nonce' ) ) {

				return;

			}

			if ( ! current_user_can( 'manage_shop_settings' ) ) {
				return;
			}

			if ( empty( $_POST['esp_settings'][ $this->item_shortname . '_license_key'] ) ) {

				delete_option( $this->item_shortname . '_license_active' );

				return;

			}

			foreach ( $_POST as $key => $value ) {
				if( false !== strpos( $key, 'license_key_deactivate' ) ) {
					// Don't activate a key when deactivating a different key
					return;
				}
			}

			$details = get_option( $this->item_shortname . '_license_active' );

			if ( is_object( $details ) && 'valid' === $details->license ) {
				return;
			}

			$license = sanitize_text_field( $_POST['esp_settings'][ $this->item_shortname . '_license_key'] );

			if( empty( $license ) ) {
				return;
			}

			// Data to send to the API
			$api_params = array(
				'esp_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Tell WordPress to look for updates
			set_site_transient( 'update_plugins', null );

			// Decode license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $this->item_shortname . '_license_active', $license_data );

		}


		/**
		 * Deactivate the license key
		 *
		 * @return  void
		 */
		public function deactivate_license() {

			if ( ! isset( $_POST['esp_settings'] ) )
				return;

			if ( ! isset( $_POST['esp_settings'][ $this->item_shortname . '_license_key'] ) )
				return;

			if( ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce'], $this->item_shortname . '_license_key-nonce' ) ) {

				wp_die( __( 'Nonce verification failed', 'email-summary-pro' ), __( 'Error', 'email-summary-pro' ), array( 'response' => 403 ) );

			}

			if( ! current_user_can( 'manage_shop_settings' ) ) {
				return;
			}

			// Run on deactivate button press
			if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate'] ) ) {

				// Data to send to the API
				$api_params = array(
					'esp_action' => 'deactivate_license',
					'license'    => $this->license,
					'item_name'  => urlencode( $this->item_name ),
					'url'        => home_url()
				);

				// Call the API
				$response = wp_remote_post(
					$this->api_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params
					)
				);

				// Make sure there are no errors
				if ( is_wp_error( $response ) ) {
					return;
				}

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				delete_option( $this->item_shortname . '_license_active' );

			}
		}


		/**
		 * Check if license key is valid once per week
		 *
		 * @since   2.5
		 * @return  void
		 */
		public function weekly_license_check() {

			if( ! empty( $_POST['esp_settings'] ) ) {
				return; // Don't fire when saving settings
			}

			if( empty( $this->license ) ) {
				return;
			}

			// data to send in our API request
			$api_params = array(
				'esp_action'=> 'check_license',
				'license' 	=> $this->license,
				'item_name' => urlencode( $this->item_name ),
				'url'       => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $this->item_shortname . '_license_active', $license_data );

		}


		/**
		 * Admin notices for errors
		 *
		 * @return  void
		 */
		public function notices() {

			static $showed_invalid_message;

			if( empty( $this->license ) ) {
				return;
			}

			if( ! current_user_can( 'manage_shop_settings' ) ) {
				return;
			}

			$messages = array();

			$license = get_option( $this->item_shortname . '_license_active' );

			if( is_object( $license ) && 'valid' !== $license->license && empty( $showed_invalid_message ) ) {

				if( empty( $_GET['tab'] ) || 'licenses' !== $_GET['tab'] ) {

					$messages[] = sprintf(
						__( 'You have invalid or expired license keys for Email Summary Pro. Please go to the <a href="%s">Licenses page</a> to correct this issue.', 'email-summary-pro' ),
						admin_url( 'edit.php?post_type=download&page=edd-settings&tab=licenses' )
					);

					$showed_invalid_message = true;

				}

			}

			if( ! empty( $messages ) ) {

				foreach( $messages as $message ) {

					echo '<div class="error">';
						echo '<p>' . $message . '</p>';
					echo '</div>';

				}

			}

		}

		/**
		 * Displays message inline on plugin row that the license key is missing
		 *
		 * @since   2.5
		 * @return  void
		 */
		public function plugin_row_license_missing( $plugin_data, $version_info ) {

			static $showed_imissing_key_message;

			$license = get_option( $this->item_shortname . '_license_active' );

			if( ( ! is_object( $license ) || 'valid' !== $license->license ) && empty( $showed_imissing_key_message[ $this->item_shortname ] ) ) {

				echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=licenses' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'email-summary-pro' ) . '</a></strong>';
				$showed_imissing_key_message[ $this->item_shortname ] = true;
			}

		}

		/**
		 * Adds this plugin to the beta page
		 *
		 * @param   array $products
		 * @since   2.6.11
		 * @return  void
		 */
		public function register_beta_support( $products ) {
			$products[ $this->item_shortname ] = $this->item_name;

			return $products;
		}

	}
endif;
