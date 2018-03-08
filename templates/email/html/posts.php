<?php
	$post_stats = esp_get_post_stats( $date_from, $date_to );
?>
<tr>
	<td style="<?php element_styles( 'td' ); ?>">

		<h2 style="<?php element_styles( 'h2' ); ?>"><?php _e( 'Posts', 'email-summary-pro' ); ?></h2>

		<p style="<?php element_styles( 'p' ); ?>">
			<?php if ( empty( $post_stats ) ) : ?>

				<?php _e( 'Absolutely nothing happened this week post wise &#128532;. Quiet week huh?', 'email-summary-pro' ); ?>

			<?php else : ?>

				<?php
					// Turn the popular date field to unix timestamp
					$date_popular_unix = strtotime( $post_stats->date_popular['date'] );
				?>

				<?php if ( $post_stats->publish_count > 0 ) : ?>

					<?php if ( 1 === $post_stats->author_count ) : ?>

						<?php echo sprintf( __( '<a target="_blank" href="%1$s">%2$s</a> published <strong>%3$s</strong> in <strong>%4$s</strong>.', 'email-summary-pro' ),
							get_author_posts_url( $post_stats->longest_post['author_id'], get_the_author_meta( 'user_nicename', $post_stats->longest_post['author_id'] ) ),
							get_the_author_meta( 'display_name', $post_stats->longest_post['author_id'] ),
							sprintf( _n( '1 post', '%s posts', $post_stats->publish_count, 'email-summary-pro' ), number_format( $post_stats->publish_count ) ),
							sprintf( _n( 'a single category', '%s categories', $post_stats->category_count, 'email-summary-pro' ), number_format( $post_stats->category_count ) )
						); ?>
					<?php else :?>

						<?php echo sprintf( __( '<strong>%1$s authors</strong> published <strong>%2$s</strong> in <strong>%3$s</strong>.', 'email-summary-pro' ),
							number_format( $post_stats->author_count ),
							sprintf( _n( '1 post', '%s posts', $post_stats->publish_count, 'email-summary-pro' ), number_format( $post_stats->publish_count ) ),
							sprintf( _n( 'a single category', '%s categories', $post_stats->category_count, 'email-summary-pro' ), number_format( $post_stats->category_count ) )
						); ?>

					<?php endif; ?>

					<?php echo sprintf(
						_n( '', 'That\'s <strong>%1$s words</strong> in total with an average of <strong>%2$s words</strong> per post.', $post_stats->publish_count, 'email-summary-pro' ),
						number_format( $post_stats->total_words ),
						number_format( $post_stats->average_words )
					); ?>

				<?php endif; ?>

				<?php if ( $post_stats->pending_count > 0 ) : ?>

					<?php echo sprintf( __( '%1$s also submitted for review.', 'email-summary-pro' ),
						sprintf( _n( '<strong>A pending post</strong> was', '<strong>%s pending posts</strong> were', $post_stats->pending_count, 'email-summary-pro' ), number_format( $post_stats->pending_count ) )
					); ?>

				<?php endif; ?>

				<?php if ( $post_stats->draft_count > 0 ) : ?>

					<?php echo sprintf( __( '<strong>%1$s</strong> have been started.', 'email-summary-pro' ),
						sprintf( _n( 'a draft post', '%s draft posts', $post_stats->draft_count, 'email-summary-pro' ), number_format( $post_stats->draft_count ) )
					); ?>

				<?php endif; ?>
				<br />
				<br />

				<?php if ( $post_stats->publish_count > 1 ) : ?>

					<?php echo sprintf(
						__( 'The longest post was <strong>%1$s</strong> (<a target="_blank" href="%2$s">%3$s</a> by %4$s) and the shortest was <strong>%5$s</strong> (<a target="_blank" href="%6$s">%7$s</a> by %8$s).', 'email-summary-pro' ),
						// Longest post.
						sprintf( _n( '%s word', '%s words', $post_stats->longest_post['word_count'], 'email-summary-pro' ), number_format( $post_stats->longest_post['word_count'] ) ),
						get_permalink( $post_stats->longest_post['post_id'] ),
						get_the_title( $post_stats->longest_post['post_id'] ),
						get_the_author_meta( 'display_name', $post_stats->longest_post['author_id'] ),
						// Shortest post.
						sprintf( _n( '%s word', '%s words', $post_stats->shortest_post['word_count'], 'email-summary-pro' ), number_format( $post_stats->shortest_post['word_count'] ) ),
						get_permalink( $post_stats->shortest_post['post_id'] ),
						get_the_title( $post_stats->shortest_post['post_id'] ),
						get_the_author_meta( 'display_name', $post_stats->shortest_post['author_id'] )
					); ?>

					<?php echo sprintf( __( '<a target="_blank" href="%1$s">%2$s</a> was the best day (%3$s published), <a target="_blank" href="%4$s">%5$s</a> was the best author (%6$s published), and <a target="_blank" href="%7$s">%8$s</a> was the most popular category (%9$s published).', 'email-summary-pro' ),
						// Most popular day.
						get_day_link( date( 'Y', $date_popular_unix ), date( 'm', $date_popular_unix ), date( 'd', $date_popular_unix ) ),
						date( 'l', $date_popular_unix ),
						sprintf( _n( '1 post', '%s posts', $post_stats->date_popular['count'], 'email-summary-pro' ), number_format( $post_stats->date_popular['count'] ) ),
						// Best author.
						get_author_posts_url( $post_stats->author_popular['author_id'], get_the_author_meta( 'user_nicename', $post_stats->author_popular['author_id'] ) ),
						get_the_author_meta( 'display_name', $post_stats->author_popular['author_id'] ),
						sprintf( _n( '1 post', '%s posts', $post_stats->author_popular['count'], 'email-summary-pro' ), number_format( $post_stats->author_popular['count'] ) ),
						// Best category.
						get_category_link( $post_stats->category_popular['category_id'] ),
						get_cat_name( $post_stats->category_popular['category_id'] ),
						sprintf( _n( '1 post', '%s posts', $post_stats->category_popular['count'], 'email-summary-pro' ), number_format( $post_stats->category_popular['count'] ) )
					); ?>

				<?php elseif ( $post_stats->publish_count === 1 ) : ?>

					<?php echo sprintf(
						__( 'The post was <a target="_blank" href="%1$s">%2$s</a> and was <strong>%3$s</strong> long. It was published on <a target="_blank" href="%4$s">%5$s</a> in <a target="_blank" href="%6$s">%7$s</a>.', 'email-summary-pro' ),
						// Post details.
						get_permalink( $post_stats->longest_post['post_id'] ),
						get_the_title( $post_stats->longest_post['post_id'] ),
						sprintf( _n( '%s word', '%s words', $post_stats->longest_post['word_count'], 'email-summary-pro' ), number_format( $post_stats->longest_post['word_count'] ) ),
						// Day the post was published.
						get_day_link( date( 'Y', $date_popular_unix ), date( 'm', $date_popular_unix ), date( 'd', $date_popular_unix ) ),
						date( 'l', $date_popular_unix ),
						// Category.
						get_category_link( $post_stats->category_popular['category_id'] ),
						get_cat_name( $post_stats->category_popular['category_id'] )
					); ?>

				<?php endif; ?>

			<?php endif; ?>
		</p>

	</td>
</tr>
