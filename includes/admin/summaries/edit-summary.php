<?php
/**
 * HTML form for editing a summary.
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

// Get the summary to edit.
// @codingStandardsIgnoreLine
if ( ! empty( $_GET['summary'] ) ) {
	$summary_id = absint( $_GET['summary'] );
} else {
	wp_die( esc_html__( 'Something went wrong.', 'email-summary-pro' ) );
}

// Load the transformer.
$summary = esp_get_summary( $summary_id );
?>
<div class="wrap">

	<h2><?php esc_html_e( 'Edit Summary', 'email-summary-pro' ); ?> - <a href="<?php echo esc_url( admin_url( 'options-general.php?page=email_summary_pro' ) ); ?>" class="button-secondary"><?php esc_html_e( 'Go Back', 'email-summary-pro' ); ?></a></h2>

	<form id="esp-edit-summary" action="" method="POST">

		<?php do_action( 'esp_edit_summary_form_top', $summary, $summary_id ); ?>

		<table class="form-table">
			<tbody>

				<?php do_action( 'esp_edit_summary_form_before_name', $summary, $summary_id ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-summary-name"><?php esc_html_e( 'Name', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-summary-name" name="name" value="<?php echo esc_attr( $summary->name ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'For identification purposes only, not displayed anywhere.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_edit_summary_form_before_status', $summary, $summary_id ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-summary-status"><?php esc_html_e( 'Status', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<select id-"esp-summary-status" name="status">
							<option value="active"<?php selected( $summary->status, 'active' ); ?>><?php esc_html_e( 'Active', 'email-summary-pro' ); ?></option>
							<option value="inactive"<?php selected( $summary->status, 'inactive' ); ?>><?php esc_html_e( 'Inactive', 'email-summary-pro' ); ?></option>
						</select>
						<p class="description"><?php esc_html_e( 'Whether the summary is active or not.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_edit_summary_form_before_recipients', $summary, $summary_id ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-recipients"><?php esc_html_e( 'Recipients', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-recipients" name="recipients" value="<?php echo esc_attr( $summary->recipients ); ?>" class="regular-text" />
						<p class="description"><?php echo sprintf( esc_html__( 'Multiple recipients can be added using commas. e.g. %s', 'email-summary-pro'), '<code>admin1@site.com, admin2@site.com</code>' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_edit_summary_form_before_subject', $summary, $summary_id ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-subject"><?php esc_html_e( 'Subject', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-subject" name="subject" value="<?php echo esc_attr( $summary->subject ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Subject of the email.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_edit_summary_form_before_title', $summary, $summary_id ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-summary-title"><?php esc_html_e( 'Title', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<input type="text" id="esp-summary-title" name="title" value="<?php echo esc_attr( $summary->title_raw ); ?>" class="regular-text" />
						<p class="description"><?php esc_html_e( 'Title shown at the top of the summary.', 'email-summary-pro' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'esp_edit_summary_form_before_disable_html', $summary, $summary_id ); ?>

				<tr>
					<th scope="row" valign="top">
						<label for="esp-disable-html"><?php esc_html_e( 'Disable HTML Emails', 'email-summary-pro' ); ?></label>
					</th>
					<td>
						<label>
							<input type="hidden" name="disable_html" value="0">
							<input type="checkbox" name="disable_html" id="esp-disable-html" class="" value="true" <?php checked( $summary->disable_html ); ?> />
							<?php esc_html_e( 'Only receive plain text emails.', 'email-summary-pro' ); ?>
						</label>
					</td>
				</tr>

			</tbody>
		</table>

		<?php do_action( 'esp_edit_summary_form_bottom', $summary, $summary_id ); ?>

		<p class="submit">
			<input type="hidden" name="summary_id" value="<?php echo absint( $summary->ID ); ?>" />
			<input type="hidden" name="esp-action" value="edit_summary" />
			<input type="hidden" name="esp-redirect" value="<?php echo esc_url( admin_url( 'options-general.php?page=email_summary_pro' ) ); ?>" />
			<input type="hidden" name="esp-summary-nonce" value="<?php echo esc_attr( wp_create_nonce( 'esp_summary_nonce' ) ); ?>" />
			<input type="submit" name="submit" value="<?php esc_html_e( 'Update Summary', 'email-summary-pro' ); ?>" class="button-primary" />
		</p>

	</form>
</div>
