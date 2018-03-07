<?php echo PHP_EOL . PHP_EOL; ?>
*<?php echo sprintf( __( '%1$s\'s Weekly Round Up', 'email-summary-pro' ), get_bloginfo( 'site_title' ) ); ?>*
<?php echo PHP_EOL . PHP_EOL; ?>
<?php echo date( "l, jS F", strtotime( $date_from ) ); ?> - <?php echo date( "l, jS F", strtotime( $date_to ) ); ?>
<?php echo PHP_EOL . PHP_EOL; ?>
<?php esc_html_e( 'Hope you had a great week! Here is your roundup of what happened on your site last week.', 'email-summary-pro' ); ?>
