<?php
	// If users can't register then don't bother
	if ( ! get_option( 'users_can_register' ) ){
		return;
	}

	$user_stats = esp_get_user_stats( $date_from, $date_to );
?>
<tr>
	<td style="<?php esp_element_style( 'td' ); ?>">

		<h2 style="<?php esp_element_style( 'h2' ); ?>"><?php _e( 'Users', 'email-summary-pro' ); ?></h2>

		<p style="<?php esp_element_style( 'p' ); ?>">

			<?php if ( ! $user_stats ) : ?>

				<?php _e( 'No one registered this week.', 'email-summary-pro' ); ?>

			<?php else : ?>

				<?php echo sprintf(
						__( '<strong>%1$s</strong> also signed up to your site.', 'email-summary-pro' ),
						sprintf(
							_n( 'A new user', '%s new users', $user_stats['registered'], 'email-summary-pro' ), number_format( $user_stats['registered'] )
						)
					);
				?>

			<?php endif; ?>
		</p>

	</td>
</tr>
