<?php
	// If users can't register then don't bother
	if ( ! get_option( 'users_can_register' ) )
		return;

	$user_stats = wp_roundup_get_user_stats( $roundup_date_from, $roundup_date_to );
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent">

					<h2><?php _e( 'Users', 'wp-roundup' ); ?></h2>

					<?php if ( ! $user_stats ) : ?>

						<?php _e( 'No one registered this week.', 'wp-roundup' ); ?>

					<?php else : ?>

						<?php echo sprintf(
								__( '<strong>%1$s</strong> also signed up to your site.', 'wp-roundup' ),
								sprintf(
									_n( 'A new user', '%s new users', $user_stats->active, 'wp-roundup' ), number_format( $user_stats->active )
								)
							);
						?>

					<?php endif; ?>

				</td>
			</tr>
		</table>
	</td>
</tr>
