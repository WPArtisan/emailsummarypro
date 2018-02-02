<?php
	$post_stats = esp_get_post_stats( $summary_date_from, $summary_date_to );
?>

<?php echo PHP_EOL . PHP_EOL; ?>

*<?php esc_html_e( 'Posts', 'email-summary-pro' ); ?>*

<?php echo PHP_EOL . PHP_EOL; ?>

<?php if ( empty( $post_stats ) ) : ?>

<?php esc_html_e( 'Absolutely nothing happened this week post wise &#128532;. Quiet week huh?', 'email-summary-pro' ); ?>

<?php else : ?>

	<?php
		// Turn the popular date field to unix timestamp
		$date_popular_unix = strtotime( $post_stats->date_popular['date'] );
	?>

<?php echo sprintf( __( '%1$s published %2$s in %3$s. That\'s %4$s words in total with an average of %5$s words per post. %6$s were submitted for review and %7$s have also been started.', 'email-summary-pro' ),
	sprintf( _n( 'an author', '%s authors', $post_stats->author_count, 'email-summary-pro' ), number_format( $post_stats->author_count ) ),
	sprintf( _n( 'a post', '%s posts', $post_stats->publish_count, 'email-summary-pro' ), number_format( $post_stats->publish_count ) ),
	sprintf( _n( 'a single category', '%s categories', $post_stats->category_count, 'email-summary-pro' ), number_format( $post_stats->category_count ) ),
	number_format( $post_stats->total_words ),
	number_format( $post_stats->average_words ),
	sprintf( _n( 'a pending post', '%s pending posts', $post_stats->pending_count, 'email-summary-pro' ), number_format( $post_stats->pending_count ) ),
	sprintf( _n( 'a draft post', '%s draft posts', $post_stats->draft_count, 'email-summary-pro' ), number_format( $post_stats->draft_count ) )
); ?>

<?php echo PHP_EOL . PHP_EOL; ?>

<?php echo sprintf( __( 'The longest post was %1$s ("%3$s" <%2$s> by %4$s) and the shortest was %5$s ("%7$s" <%6$s> by %8$s). %10$s <%9$s> was the best day (%11$s), %13$s <%12$s> was the best author (%14$s), and %16$s <%15$s> was the most popular category (%17$s).', 'email-summary-pro' ),
	sprintf( _n( '%s word', '%s words', $post_stats->longest_post['word_count'], 'email-summary-pro' ), number_format( $post_stats->longest_post['word_count'] ) ),
	get_permalink( $post_stats->longest_post['post_id'] ),
	get_the_title( $post_stats->longest_post['post_id'] ),
	get_the_author_meta( 'display_name', $post_stats->longest_post['author_id'] ),

	sprintf( _n( '%s word', '%s words', $post_stats->shortest_post['word_count'], 'email-summary-pro' ), number_format( $post_stats->shortest_post['word_count'] ) ),
	get_permalink( $post_stats->shortest_post['post_id'] ),
	get_the_title( $post_stats->shortest_post['post_id'] ),
	get_the_author_meta( 'display_name', $post_stats->shortest_post['author_id'] ),

	get_day_link( date("Y", $date_popular_unix ), date("m", $date_popular_unix ), date("d", $date_popular_unix ) ),
	date( "l", $date_popular_unix ),
	sprintf( _n( 'a post', '%s posts', $post_stats->date_popular['count'], 'email-summary-pro' ), number_format( $post_stats->date_popular['count'] ) ),

	get_author_posts_url( $post_stats->author_popular['author_id'], get_the_author_meta( 'user_nicename', $post_stats->author_popular['author_id'] ) ),
	get_the_author_meta( 'display_name', $post_stats->author_popular['author_id'] ),
	sprintf( _n( 'a post', '%s posts', $post_stats->author_popular['count'], 'email-summary-pro' ), number_format( $post_stats->author_popular['count'] ) ),

	get_category_link( $post_stats->category_popular['category_id'] ),
	get_cat_name( $post_stats->category_popular['category_id'] ),
	sprintf( _n( 'a post', '%s posts', $post_stats->category_popular['count'], 'email-summary-pro' ), number_format( $post_stats->category_popular['count'] ) )
); ?>

<?php endif; ?>
