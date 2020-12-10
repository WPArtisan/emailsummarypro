<?php
/**
 * Extensions page class.
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
class Email_Summary_Pro_Admin_Extensions extends Email_Summary_Pro_Admin_Base {

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
			'extensions',
			esc_html__( 'Extensions', 'email-summary-pro' ),
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
		?>
		<style>
		.esp-grid-container {
			margin-top: 25px;
			display: grid;
			grid-column-gap: 25px;
			grid-template-columns: auto auto auto;
		}
		.esp-grid-item {
			background: white;
			border-radius: 2px;
			border: 1px solid lightgray;
			margin-bottom: 30px;
		}
		.esp-grid-item h2 {
			margin-left: 15px;
		}
		.esp-grid-item h2 a {
			text-decoration: none;
			font-size: 1.25rem;
			font-weight: bold;
			color: #444;
		}
		.esp-grid-item h2 a:hover {
		}
		.esp-grid-item img {
			display: block;
			max-width: 100%;
		}
		.esp-body {
			padding: 0 15px 15px 15px;
		}
		.esp-cta-button {
			margin-top: 30px !important;
		}
		.esp-license-input {
			width: 100%;
		}
		</style>

		<h2>
			<?php esc_html_e( 'Apps and Integrations for Email Summary Pro', 'email-summary-pro' ); ?>
			<a class="button button-primary" target="_blank" href="https://emailsummarypro.com/extensions/?utm_source=plugin&utm_medium=extensions-a"><?php esc_html_e( 'Browse all extensions', 'email-summary-pro' ); ?></a>
		</h2>

		<p>
			<?php echo wp_kses( __( 'These extensions <b>add extra functionality</b> to your email summaries.', 'email-summary-pro' ), array( 'b' => true ) ); ?>
		</p>

		<form method="post" action="options.php">
			<div class="esp-grid-container">
				<?php foreach ( $this->get_extentions() as $extension ) : ?>
					<div class="esp-grid-item">
						<h2>
							<a href="<?php echo esc_html( $extension['link'] ); ?>" target="_blank">
								<?php echo esc_html( $extension['name'] ); ?>
							</a>
						</h2>
						<a href="<?php echo esc_html( $extension['link'] ); ?>" target="_blank">
							<img src="<?php echo esc_html( $extension['image'] ); ?>" alt="" />
						</a>
						<div class="esp-body">
							<p>
								<?php echo esc_html( $extension['description'] ); ?>
							</p>

							<?php if ( apply_filters( 'esp_extension_active_' . $extension['item_id'], false ) ) : ?>

								<input type="text" id="license_key" name="wpna_license_key" value="" class="esp-license-input">
								<p><?php echo esc_html_e( 'Please enter your licence key to enable updates.', 'email-summary-pro' ); ?><p>

							<?php else :?>
								<a class="button button-secondary esp-cta-button" href="<?php echo esc_html( $extension['link'] ); ?>" target="_blank">
									<?php echo esc_html_e( 'Get this extension', 'email-summary-pro' ); ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>

			</div>

			<?php wp_nonce_field( 'esp_licenses-save', '__wpna_licenses_nonce' ); ?>
			<input type="hidden" name="esp-action" value="save_licenses" />
			<input type="submit" class="button-primary" name="activate_license" value="<?php esc_html_e( 'Save Changes', 'email-summary-pro' ); ?>"/>
		</form>
		<?php
	}

	/**
	 * List of all available extensions.
	 *
	 * @return array
	 */
	public function get_extentions() {

		$extensions = array(
			array(
				'name'        => 'Template Manager',
				'item_id'     => 12,
				'description' => 'Manage the template parts that are shown in your summaries and in what the order.',
				'image'       => 'https://8333-presscdn-0-98-pagely.netdna-ssl.com/wp-content/uploads/edd/2016/04/stripe-featured-image.png',
				'link'        => 'https://emailsummarypro.com/extensions/template-manager/?utm_source=plugin&utm_medium=extensions-b',
				'active'      => false,
			),
			array(
				'name'        => 'Summary Frequency',
				'item_id'     => 13,
				'description' => 'Adjust the frequency of your summaries. Hourly, Daily, Monthly, as often as you like',
				'image'       => 'https://8333-presscdn-0-98-pagely.netdna-ssl.com/wp-content/uploads/edd/2016/04/stripe-featured-image.png',
				'link'        => 'https://emailsummarypro.com/extensions/summary-frequency/?utm_source=plugin&utm_medium=extensions-b',
				'active'      => false,
			),
			array(
				'name'        => 'Users as Recipients',
				'item_id'     => 13,
				'description' => 'Set the Recipients as WordPress user levels.',
				'image'       => 'https://8333-presscdn-0-98-pagely.netdna-ssl.com/wp-content/uploads/edd/2016/04/stripe-featured-image.png',
				'link'        => 'https://emailsummarypro.com/extensions/summary-frequency/?utm_source=plugin&utm_medium=extensions-b',
				'active'      => false,
			),
			array(
				'name'        => 'Template Builder',
				'item_id'     => 13,
				'description' => 'Build custom Template parts right from the WP Admin.',
				'image'       => 'https://8333-presscdn-0-98-pagely.netdna-ssl.com/wp-content/uploads/edd/2016/01/recurring-payments-product-image.png',
				'link'        => 'https://emailsummarypro.com/extensions/author-summary/?utm_source=plugin&utm_medium=extensions-b',
				'active'      => false,
			),
			array(
				'name'        => 'Author Summary',
				'item_id'     => 13,
				'description' => 'Send Authors summarys of how their posts are doing.',
				'image'       => 'https://8333-presscdn-0-98-pagely.netdna-ssl.com/wp-content/uploads/edd/2016/01/recurring-payments-product-image.png',
				'link'        => 'https://emailsummarypro.com/extensions/author-summary/?utm_source=plugin&utm_medium=extensions-b',
				'active'      => false,
			),
			array(
				'name'        => 'Google Analytics',
				'item_id'     => 13,
				'description' => 'Add Google Analytics information to your Summary Emails.',
				'image'       => 'https://8333-presscdn-0-98-pagely.netdna-ssl.com/wp-content/uploads/2015/08/software-licensing-product-image.png',
				'link'        => 'https://emailsummarypro.com/extensions/google-analytics/?utm_source=plugin&utm_medium=extensions-b',
				'active'      => false,
			),
		);

		/**
		 * Filter the available extensions.
		 *
		 * @var array
		 */
		$extensions = apply_filters( 'esp_registered_extensions', $extensions );

		return $extensions;
	}

}
