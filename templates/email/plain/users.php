<?php
// If users can't register then don't bother
if ( ! get_option( 'users_can_register' ) )
	return;

$user_stats = esp_get_user_stats( $date_from, $date_to );
?>

<?php echo PHP_EOL . PHP_EOL; ?>

*<?php esc_html_e( 'Users', 'email-summary-pro' ); ?>*

<?php echo PHP_EOL . PHP_EOL; ?>

<?php if ( ! $user_stats ) : ?>

<?php esc_html_e( 'No one registered this week.', 'email-summary-pro' ); ?>

<?php else : ?>

<?php echo sprintf(
		__( '%1$s also signed up to your site.', 'email-summary-pro' ),
		sprintf(
			_n( 'A new user', '%s new users', $user_stats->active, 'email-summary-pro' ), number_format( $user_stats->active )
		)
	);
?>

<?php endif; ?>
