<?php
	$update_data = wp_get_update_data();

	if ( ! $update_data['counts']['total'] ) {
		return;
	}
?>
<tr>
	<td style="<?php element_styles( 'td' ); ?>">

		<h2 style="<?php element_styles( 'h2' ); ?>"><?php esc_html_e( 'Updates', 'email-summary-pro' ); ?></h2>

		<p style="<?php element_styles( 'p' ); ?>">
			<a target="_blank" href="<?php echo esc_url( admin_url( '/update-core.php' ) ); ?>"><?php echo esc_html( $update_data['title'] ); ?></a>
		</p>

	</td>
</tr>
