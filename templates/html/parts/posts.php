<?php
	$post_stats = wp_roundup_get_post_stats( $roundup_date_from, $roundup_date_to );
?>
<tr>
	<td align="center" valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
			<tr>
				<td valign="top" class="bodyContent">

					<h2><?php _e( 'Posts', 'wp-roundup' ); ?></h2>

					<?php if ( empty( $post_stats ) ) : ?>

						<?php _e( 'Absolutely nothing happened this week post wise &#128532;. Quiet week huh?', 'wp-roundup' ); ?>

					<?php else : ?>

						<?php
							// Turn the popular date field to unix timestamp
							$date_popular_unix = strtotime( $post_stats->date_popular['date'] );
						?>

						<?php echo sprintf( __( '<strong>%1$s</strong> published <strong>%2$s</strong> in <strong>%3$s</strong>. That\'s <strong>%4$s words</strong> in total with an average of <strong>%5$s words</strong> per post. <strong>%6$s</strong> were submitted for review and <strong>%7$s</strong> have also been started.', 'wp-roundup' ),
							sprintf( _n( 'an author', '%s authors', $post_stats->author_count, 'wp-roundup' ), number_format( $post_stats->author_count ) ),
							sprintf( _n( 'a post', '%s posts', $post_stats->publish_count, 'wp-roundup' ), number_format( $post_stats->publish_count ) ),
							sprintf( _n( 'a single category', '%s categories', $post_stats->category_count, 'wp-roundup' ), number_format( $post_stats->category_count ) ),
							number_format( $post_stats->total_words ),
							number_format( $post_stats->average_words ),
							sprintf( _n( 'a pending post', '%s pending posts', $post_stats->pending_count, 'wp-roundup' ), number_format( $post_stats->pending_count ) ),
							sprintf( _n( 'a draft post', '%s draft posts', $post_stats->draft_count, 'wp-roundup' ), number_format( $post_stats->draft_count ) )
						); ?>
						<br />
						<br />
						<?php echo sprintf( __( 'The longest post was <strong>%1$s</strong> (<a href="%2$s">%3$s</a> by %4$s) and the shortest was <strong>%5$s</strong> (<a href="%6$s">%7$s</a> by %8$s). <a href="%9$s">%10$s</a> was the best day (%11$s), <a href="%12$s">%13$s</a> was the best author (%14$s), and <a href="%15$s">%16$s</a> was the most popular category (%17$s).', 'wp-roundup' ),
							sprintf( _n( '%s word', '%s words', $post_stats->longest_post['word_count'], 'wp-roundup' ), number_format( $post_stats->longest_post['word_count'] ) ),
							get_permalink( $post_stats->longest_post['post_id'] ),
							get_the_title( $post_stats->longest_post['post_id'] ),
							get_the_author_meta( 'display_name', $post_stats->longest_post['author_id'] ),

							sprintf( _n( '%s word', '%s words', $post_stats->shortest_post['word_count'], 'wp-roundup' ), number_format( $post_stats->shortest_post['word_count'] ) ),
							get_permalink( $post_stats->shortest_post['post_id'] ),
							get_the_title( $post_stats->shortest_post['post_id'] ),
							get_the_author_meta( 'display_name', $post_stats->shortest_post['author_id'] ),

							get_day_link( date("Y", $date_popular_unix ), date("m", $date_popular_unix ), date("d", $date_popular_unix ) ),
							date( "l", $date_popular_unix ),
							sprintf( _n( 'a post', '%s posts', $post_stats->date_popular['count'], 'wp-roundup' ), number_format( $post_stats->date_popular['count'] ) ),

							get_author_posts_url( $post_stats->author_popular['author_id'], get_the_author_meta( 'user_nicename', $post_stats->author_popular['author_id'] ) ),
							get_the_author_meta( 'display_name', $post_stats->author_popular['author_id'] ),
							sprintf( _n( 'a post', '%s posts', $post_stats->author_popular['count'], 'wp-roundup' ), number_format( $post_stats->author_popular['count'] ) ),

							get_category_link( $post_stats->category_popular['category_id'] ),
							get_cat_name( $post_stats->category_popular['category_id'] ),
							sprintf( _n( 'a post', '%s posts', $post_stats->category_popular['count'], 'wp-roundup' ), number_format( $post_stats->category_popular['count'] ) )
						); ?>

					<?php endif; ?>

				</td>
			</tr>
		</table>
	</td>
</tr>
