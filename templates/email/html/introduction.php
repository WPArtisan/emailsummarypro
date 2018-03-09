<tr>
	<td style="<?php esp_element_style( 'td' ); ?>">
		<h1 style="<?php esp_element_style( 'h1' ); ?>">
			<center>
				<?php echo $title; ?>
			</center>
		</h1>

		<h2 style="<?php esp_element_style( 'h2' ); ?>">
			<center>
				<?php echo date( "l, jS F", strtotime( $date_from ) ); ?>
				&nbsp;-&nbsp;
				<?php echo date( "l, jS F", strtotime( $date_to ) ); ?>
			</center>
		</h2>

		<br />
		<p style="<?php esp_element_style( 'p' ); ?>"><?php esc_html_e( 'Hope you had a great week! Here is your summary of what happened on your site last week.', 'email-summary-pro' ); ?></p>
	</td>
</tr>
