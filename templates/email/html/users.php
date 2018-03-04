<?php
	// If users can't register then don't bother
	if ( ! get_option( 'users_can_register' ) )
		return;

	$user_stats = esp_get_user_stats( $summary_date_from, $summary_date_to );
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent">

					<h2><?php _e( 'Users', 'email-summary-pro' ); ?></h2>

					<?php if ( ! $user_stats ) : ?>

						<?php _e( 'No one registered this week.', 'email-summary-pro' ); ?>

					<?php else : ?>

						<?php echo sprintf(
								__( '<strong>%1$s</strong> also signed up to your site.', 'email-summary-pro' ),
								sprintf(
									_n( 'A new user', '%s new users', $user_stats->active, 'email-summary-pro' ), number_format( $user_stats->active )
								)
							);
						?>

					<?php endif; ?>

				</td>
			</tr>
		</table>
	</td>
</tr>
