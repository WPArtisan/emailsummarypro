<?php
	$update_data = wp_get_update_data();

	if ( ! $update_data['counts']['total'] ) {
		return;
	}
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent">

					<h2><?php esc_html_e( 'Updates', 'email-summary-pro' ); ?></h2>

					<?php echo esc_html( $update_data['title'] ); ?>

				</td>
			</tr>
		</table>
	</td>
</tr>
