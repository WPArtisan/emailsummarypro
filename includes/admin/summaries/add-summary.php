<?php
/**
 * HTML form for adding a new summary.
 *
 * @package     email-summary-pro
 * @subpackage  Admin/Summaries
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">

	<h2><?php esc_html_e( 'Add New Summary', 'email-summary-pro' ); ?> - <a href="<?php echo esc_url( admin_url( 'options-general.php?page=email_summary_pro' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Go Back', 'email-summary-pro' ); ?></a></h2>

	<form id="esp-add-summary" action="" method="POST">

		<?php do_action( 'esp_add_summary_form_top' ); ?>

		<table class="form-table">
			<tbody>

				<?php do_action( 'esp_add_summary_form_before_name' ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-summary-name"><?php esc_html_e( 'Name', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-summary-name" name="name" value="" class="regular-text" />
						<p class="description"><?php esc_html_e( 'The name of this summary.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_add_summary_form_before_status' ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-summary-status"><?php esc_html_e( 'Status', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<select id-"esp-summary-status" name="status">
							<option value="active"><?php esc_html_e( 'Active', 'email-summary-pro' ); ?></option>
							<option value="inactive"><?php esc_html_e( 'Inactive', 'email-summary-pro' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'Whether the summary is active or not.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_add_summary_form_before_recipients' ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-recipients"><?php esc_html_e( 'Recipients', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-recipients" name="recipients" value="" class="regular-text" />
						<p class="description"><?php echo sprintf( esc_html__( 'Multiple recipients can be added using commas. e.g. %s', 'email-summary-pro'), '<code>admin1@site.com, admin2@site.com</code>' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_add_summary_form_before_subject' ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-subject"><?php esc_html_e( 'Subject', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-subject" name="subject" value="" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Subject of the email.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_add_summary_form_before_disable_html' ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-disable-html-emails"><?php esc_html_e( 'Disable HTML Emails', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="hidden" name="disable_html" value="0">
						<input type="checkbox" name="disable_html" id="esp-disable-html-emails" class="" value="true" />
						<p class="description"><?php esc_html_e( 'Disable HTML emails and only recieve plain text ones.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

			</tbody>
		</table>

		<?php do_action( 'esp_add_summary_form_bottom' ); ?>

		<p class="submit">
			<input type="hidden" name="esp-action" value="add_summary" />
			<input type="hidden" name="esp-redirect" value="<?php echo esc_url( admin_url( 'options-general.php?page=email_summary_pro' ) ); ?>" />
			<input type="hidden" name="esp-summary-nonce" value="<?php echo esc_attr( wp_create_nonce( 'esp_summary_nonce' ) ); ?>" />
			<input type="submit" name="submit" value="<?php esc_html_e( 'Add Summary', 'email-summary-pro' ); ?>" class="button-primary" />
		</p>

	</form>
</div>
