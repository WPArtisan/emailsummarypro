<?php
	$comment_stats = esp_get_comment_stats( $roundup_date_from, $roundup_date_to );
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent">

					<h2><?php _e( 'Comments', 'wp-roundup' ); ?></h2>

					<?php if ( ! $comment_stats ) : ?>

						<?php _e( 'No comment action this week.', 'wp-roundup' ); ?>

					<?php else : ?>

						<?php echo sprintf(
								__( 'You had <strong>%1$s</strong>', 'wp-roundup' ),
								sprintf(
									_n( '1 approved comment', '%s approved comments', $comment_stats->approved_comments, 'wp-roundup' ), number_format( $comment_stats->approved_comments )
								)
							);
						?>

						<?php echo sprintf(
								__( 'with <a href="%1$s">%2$s</a> being the most popular post (%3$s).', 'wp-roundup' ),
								get_permalink( $comment_stats->popular_post['post_id'] ),
								get_the_title( $comment_stats->popular_post['post_id'] ),
								sprintf(
									_n( '1 comment', '%s comments', $comment_stats->popular_post['count'], 'wp-roundup' ), number_format( $comment_stats->popular_post['count'] )
								)
							);
						?>

						<?php echo sprintf(
								__( '<strong>%1$s</strong> are also waiting to be approved.', 'wp-roundup' ),
								sprintf(
									_n( '1 comment', '%s comments', $comment_stats->pending_comments, 'wp-roundup' ), number_format( $comment_stats->pending_comments )
								)
							);
						?>

					<?php endif;?>

				</td>
			</tr>
		</table>
	</td>
</tr>
