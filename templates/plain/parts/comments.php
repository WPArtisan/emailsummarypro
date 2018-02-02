<?php
	$comment_stats = esp_get_comment_stats( $summary_date_from, $summary_date_to );
?>

<?php echo PHP_EOL . PHP_EOL; ?>

*<?php esc_html_e( 'Comments', 'email-summary-pro' ); ?>*

<?php echo PHP_EOL . PHP_EOL; ?>

<?php if ( ! $comment_stats ) : ?>

<?php esc_html_e( 'No comment action this week.', 'email-summary-pro' ); ?>

<?php else : ?>

<?php echo sprintf(
	__( 'You had %1$s', 'email-summary-pro' ),
	sprintf(
		_n( '1 approved comment', '%s approved comments', $comment_stats->approved_comments, 'email-summary-pro' ), number_format( $comment_stats->approved_comments )
	)
);
?>
<?php echo sprintf(
	__( 'with "%2$s" <%1$s> being the most popular post (%3$s).', 'email-summary-pro' ),
	get_permalink( $comment_stats->popular_post['post_id'] ),
	get_the_title( $comment_stats->popular_post['post_id'] ),
	sprintf(
		_n( '1 comment', '%s comments', $comment_stats->popular_post['count'], 'email-summary-pro' ), number_format( $comment_stats->popular_post['count'] )
	)
);
?>
<?php echo sprintf(
	__( '%1$s are also waiting to be approved.', 'email-summary-pro' ),
	sprintf(
		_n( '1 comment', '%s comments', $comment_stats->pending_comments, 'email-summary-pro' ), number_format( $comment_stats->pending_comments )
	)
);
?>

<?php endif;?>
