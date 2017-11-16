<tr>
	<td align="center" valign="top">
		<!-- BEGIN BODY // -->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent" mc:edit="body_content">
					<h1>
						<center>
							<?php echo sprintf(
									__( '<a href="%1$s"><strong>%2$s\'s</strong></a> Weekly Round Up', 'wp-roundup' ),
									esc_url( get_bloginfo('url') ),
									get_bloginfo('site_title')
								);
							?>
						</center>
					</h1>

					<h2>
						<center>
							<?php echo date( "l, jS F", strtotime( $roundup_date_from ) ); ?>
							&nbsp;&#150;&nbsp;
							<?php echo date( "l, jS F", strtotime( $roundup_date_to ) ); ?>
						</center>
					</h2>

					<br />
					<?php _e( 'Hope you had a great week! Here is your roundup of what happened on your site last week.', 'wp-roundup' ); ?>

				</td>
			</tr>
		</table>
		<!-- // END BODY -->
	</td>
</tr>
