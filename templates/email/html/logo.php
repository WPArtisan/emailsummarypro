<?php
// https://codex.wordpress.org/Theme_Logo
if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo           = wp_get_attachment_image_src( $custom_logo_id , 'full' );
	// $logo_image_tag = $logo[0];
} else {
	$logo_url = ESP_PLUGIN_URL . 'assets/img/clipboard.png';
}
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="logoHeader">
			<tr>
				<td valign="top">
					<center>
						<img src="<?php echo esc_url( $logo_url ); ?>" style="max-width:128px;" id="logo" />
					</center>
				</td>
			</tr>
		</table>
	</td>
</tr>
