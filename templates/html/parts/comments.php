<?php
	$comment_stats = esp_get_comment_stats( $summary_date_from, $summary_date_to );
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent">

					<h2><?php _e( 'Comments', 'email-summary-pro' ); ?></h2>

					<?php if ( ! $comment_stats ) : ?>
						<i><?php _e( 'No comment action this week.', 'email-summary-pro' ); ?></i>

					<?php else : ?>

						<?php echo sprintf(
								__( 'You had <strong>%1$s</strong>', 'email-summary-pro' ),
								sprintf(
									_n( '1 approved comment', '%s approved comments', $comment_stats->approved_comments, 'email-summary-pro' ), number_format( $comment_stats->approved_comments )
								)
							);
						?>

						<?php echo sprintf(
								__( 'with <a target="_blank" href="%1$s">%2$s</a> being the most popular post (%3$s).', 'email-summary-pro' ),
								get_permalink( $comment_stats->popular_post['post_id'] ),
								get_the_title( $comment_stats->popular_post['post_id'] ),
								sprintf(
									_n( '1 comment', '%s comments', $comment_stats->popular_post['count'], 'email-summary-pro' ), number_format( $comment_stats->popular_post['count'] )
								)
							);
						?>

						<?php echo sprintf(
								__( '<strong>%1$s</strong> are also waiting to be approved.', 'email-summary-pro' ),
								sprintf(
									_n( '1 comment', '%s comments', $comment_stats->pending_comments, 'email-summary-pro' ), number_format( $comment_stats->pending_comments )
								)
							);
						?>

					<?php endif;?>

				</td>
			</tr>
		</table>
	</td>
</tr>
