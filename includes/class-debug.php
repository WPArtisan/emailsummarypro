<?php
/**
 * Debug page class.
 *
 * @since 1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Extensions class for the plugin.
 *
 * Sets up all extensions page and deals with liceses.
 *
 * @since  1.0.0
 */
class Email_Summary_Pro_Debug extends Email_Summary_Pro_Admin_Base {

	/**
	 * Hooks registered in this class.
	 *
	 * @access public
	 * @return void
	 */
	public function hooks() {
		add_action( 'esp_admin_tabs', array( $this, 'setup_tabs' ), 10, 1 );
	}

	/**
	 * Add the extensions tab to the admin page.
	 *
	 * @access public
	 * @return void
	 */
	public function setup_tabs( $page_url ) {
		email_summary_pro()->tabs_helper->register_tab(
			'debug',
			esc_html__( 'Debug', 'email-summary-pro' ),
			$page_url,
			array( $this, 'tab_callback' ),
			true
		);
	}

	/**
	 * Output the HTML for the Extensions tab.
	 *
	 * @access public
	 * @return void
	 */
	public function tab_callback() {
		$email_errors = get_transient( 'esp_email_errors' );
		?>

		<h2>
			<?php esc_html_e( 'Errors Logged', 'email-summary-pro' ); ?>
		</h2>
		<textarea style="width: 100%; padding: 20px;">
			<?php if ( ! empty( $email_errors ) ) : ?>
				<?php foreach ( $email_errors as $error ) :?>
					<?php echo esc_html( $error ); ?>
				<?php endforeach;?>
			<?php endif;?>
		</textarea>

		<h2>
			<?php esc_html_e( 'System Information', 'email-summary-pro' ); ?>
		</h2>

		<?php
	}

}
