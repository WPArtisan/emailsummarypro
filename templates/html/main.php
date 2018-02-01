<?php
	/**
	 * Include the header template.
	 *
	 * Template based on the Mailchimp blueprint email templates.
	 * @link https://github.com/mailchimp/email-blueprints
	 */
	include esp_locate_template( 'html/header.php' );
?>

<center>
	<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
		<tr>
			<td align="center" valign="top" id="bodyCell">
				<!-- BEGIN TEMPLATE // -->
				<table border="0" cellpadding="0" cellspacing="0" id="templateContainer">

					<?php
					foreach ( $template_parts as $template_part ) : ?>
						<?php
						if ( file_exists( esp_locate_template( $template_part ) ) )
							include esp_locate_template( $template_part );
						?>
					<?php endforeach; ?>

				</table>
				<!-- // END TEMPLATE -->
			</td>
		</tr>
	</table>
</center>

<?php
	/**
	 * Include the footer template.
	 */
	include esp_locate_template( 'html/footer.php' );
?>
