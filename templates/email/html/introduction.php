<tr>
	<td style="<?php element_styles( 'td' ); ?>">
		<h1 style="<?php element_styles( 'h1' ); ?>">
			<center>
				<?php echo sprintf(
						__( '<a target="_blank" href="%1$s"><strong>%2$s\'s</strong></a> Weekly Round Up', 'email-summary-pro' ),
						esc_url( get_bloginfo('url') ),
						get_bloginfo( 'site_title' )
					);
				?>
			</center>
		</h1>

		<h2 style="<?php element_styles( 'h2' ); ?>">
			<center>
				<?php echo date( "l, jS F", strtotime( $date_from ) ); ?>
				&nbsp;-&nbsp;
				<?php echo date( "l, jS F", strtotime( $date_to ) ); ?>
			</center>
		</h2>

		<br />
		<p style="<?php element_styles( 'p' ); ?>"><?php esc_html_e( 'Hope you had a great week! Here is your summary of what happened on your site last week.', 'email-summary-pro' ); ?></p>
	</td>
</tr>
