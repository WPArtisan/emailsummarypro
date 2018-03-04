<?php
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
	if ( ! function_exists( 'has_custom_logo' ) || ! has_custom_logo() ) {
		return;
	}
?>
<tr>
	<td align="center" valign="top">
		<!-- BEGIN HEADER // -->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="logoHeader">
			<tr>
				<td valign="top" class="headerContent">

					<!-- https://codex.wordpress.org/Theme_Logo -->
					<br />
					<center><img src="<?php echo esc_url( $logo[0] ); ?>" style="max-width:128px;" id="headerImage" /></center>
					<br />

				</td>
			</tr>
		</table>
		<!-- // END HEADER -->
	</td>
</tr>
