<?php
	$comment_stats = esp_get_comment_stats( $date_from, $date_to );
?>
<tr>
	<td style="<?php esp_element_style( 'td' ); ?>">

		<h2 style="<?php esp_element_style( 'h2' ); ?>"><?php esc_html_e( 'Comments', 'email-summary-pro' ); ?></h2>

		<p style="<?php esp_element_style( 'p' ); ?>">
			<?php if ( ! $comment_stats ) : ?>

				<i><?php _e( 'No comment action this week.', 'email-summary-pro' ); ?></i>

			<?php else : ?>

				<?php echo sprintf(
						__( 'You had <strong>%1$s</strong>', 'email-summary-pro' ),
						sprintf(
							_n( '1 approved {site_title} comment', '%s approved comments', $comment_stats['comments_approved'], 'email-summary-pro' ), number_format( $comment_stats['comments_approved'] )
						)
					);
				?>

				<?php echo sprintf(
						__( 'with <a target="_blank" href="%1$s">%2$s</a> being the most popular post (%3$s).', 'email-summary-pro' ),
						get_permalink( $comment_stats['comments_post_popular']['post_id'] ),
						get_the_title( $comment_stats['comments_post_popular']['post_id'] ),
						sprintf(
							_n( '1 comment', '%s comments', $comment_stats['comments_post_popular']['count'], 'email-summary-pro' ), number_format( $comment_stats['comments_post_popular']['count'] )
						)
					);
				?>

				<?php if ( $comment_stats['comments_pending'] > 0 ) : ?>
					<?php echo sprintf(
							__( '<strong>%1$s</strong> are also waiting to be approved.', 'email-summary-pro' ),
							sprintf(
								_n( '1 comment', '%s comments', $comment_stats['comments_pending'], 'email-summary-pro' ), number_format( $comment_stats['comments_pending'] )
							)
						);
					?>
				<?php endif; ?>

			<?php endif;?>
		</p>

	</td>
</tr>
