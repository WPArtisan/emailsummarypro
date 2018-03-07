<tr>
	<td align="center" valign="top">
		<!-- BEGIN BODY // -->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent" mc:edit="body_content">
					<h1>
						<center>
							<?php echo sprintf(
									__( '<a target="_blank" href="%1$s"><strong>%2$s\'s</strong></a> Weekly Round Up', 'email-summary-pro' ),
									esc_url( get_bloginfo('url') ),
									get_bloginfo( 'site_title' )
								);
							?>
						</center>
					</h1>

					<h2>
						<center>
							<?php echo date( "l, jS F", strtotime( $date_from ) ); ?>
							&nbsp;-&nbsp;
							<?php echo date( "l, jS F", strtotime( $date_to ) ); ?>
						</center>
					</h2>

					<br />
					<?php esc_html_e( 'Hope you had a great week! Here is your summary of what happened on your site last week.', 'email-summary-pro' ); ?>

				</td>
			</tr>
		</table>
		<!-- // END BODY -->
	</td>
</tr>
