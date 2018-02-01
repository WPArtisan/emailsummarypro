<?php

/**
 * Optimised functions for getting all the required stats.
 *
 * @author OzTheGreat
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'esp_get_post_stats' ) ) :

	/**
	 * Retrives stats for a post type.
	 *
	 * Runs optimised SQL queries to get all the stats for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $date_from The start date of the stats
	 * @param string $date_to The end date of the stats (inclusive)
	 * @return object
	 */
	function esp_get_post_stats( $date_from, $date_to ) {

		// Create a unique cache key
		$key = 'esp_post_stats_' . md5( $date_from, $date_to );

		// See if it's cached or not
		if ( $stats = wp_cache_get( $key, 'esp' ) )
			return $stats;

		global $wpdb;

		// Holds that stats.
		// Set default values.
		$stats = array(
			'author_count'  => 0,
			'publish_count' => 0,
			'pending_count' => 0,
			'draft_count'   => 0,
			'total_words'   => 0,
			'average_words' => 0,
			'longest_post'  => array(
				'post_id'    => 0,
				'author_id'  => 0,
				'word_count' => 0,
			),
			'shortest_post' => array(
				'post_id'    => 0,
				'author_id'  => 0,
				'word_count' => 99999999, // Start with an unlikley large number
			),
			'author_popular' => array(
				'author_id' => 0,
				'count'     => 0,
			),
			'category_popular' => array(
				'category_id' => 0,
				'count'       => 0,
			),
			'date_popular' => array(
				'date'  => null,
				'count' => 0,
			),

			// Contains the daily breakdown
			'breakdown' => array(),
		);

		// Get all the content for our date range.
		$posts = $wpdb->get_results(
			$wpdb->prepare(
			"SELECT
				{$wpdb->posts}.ID,
				{$wpdb->posts}.post_author,
				{$wpdb->posts}.post_content,
				{$wpdb->posts}.post_date,
				{$wpdb->posts}.post_status,
				{$wpdb->term_taxonomy}.term_id
			FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)
			LEFT JOIN {$wpdb->term_taxonomy} ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
			WHERE {$wpdb->posts}.post_date >= %s
				AND {$wpdb->posts}.post_date <= %s
				AND {$wpdb->posts}.post_type = 'post'
				AND {$wpdb->posts}.post_status IN ( 'publish', 'pending', 'draft')
				AND {$wpdb->term_taxonomy}.taxonomy = 'category'
			GROUP BY {$wpdb->posts}.ID
			",
				$date_from,
				$date_to
			)
		);

		// No posts found, return default stats.
		if ( empty( $posts) )
			return null;

		// Holds the post authors so we can count them later
		$post_authors = array();

		// Holds the post categories so we can count them later
		$post_categories = array();

		// Loop through all our content and work out the stats
		foreach ( $posts as $post ) {

			// Format the post date for the array
			$post_day_date = date( "Y-m-d", strtotime( $post->post_date ) );

			// Create the day in the array if it doesn't exist
			if ( empty( $stats['breakdown'][ $post_day_date ] ) ) {

				$stats['breakdown'][ $post_day_date ] = array(
					'publish_count' => 0,
					'pending_count' => 0,
					'draft_count'   => 0,
					'total_words'   => 0,
					'longest_post'  => array(
						'post_id'    => 0,
						'author_id'  => 0,
						'word_count' => 0,
					),
					'shortest_post' => array(
						'post_id'    => 0,
						'author_id'  => 0,
						'word_count' => 99999999, // Start with an unlikley large number
					),
				);

			}

			// If it's a draft post, increase the draft count then bail
			if ( 'draft' == $post->post_status ) {
				$stats['draft_count']++;
				$stats['breakdown'][ $post_day_date ]['draft_count']++;
				continue;

			// If it's a pending post, increase the pending count then bail
			} elseif ( 'pending' == $post->post_status ) {
				$stats['pending_count']++;
				$stats['breakdown'][ $post_day_date ]['pending_count']++;
				continue;
			}

			// Strip non-words from the content
			$sanitized_content = strip_shortcodes( strip_tags( $post->post_content ) );

			// Word count for this content
			$content_word_count = str_word_count( $sanitized_content );

			// Add to the total word count
			$stats['total_words'] += $content_word_count;

			// Add to the total publish count
			$stats['publish_count']++;

			// Add the author to the author array.
			// We'll unique it later.
			$post_authors[] = $post->post_author;

			// Add the category to the category array.
			// We'll unique it later.
			$post_categories[] = $post->term_id;

			// Check if it's the longest article ever
			if ( $content_word_count > $stats['longest_post']['word_count'] ) {
				$stats['longest_post']['post_id'] = $post->ID;
				$stats['longest_post']['author_id'] = $post->post_author;
				$stats['longest_post']['word_count'] = $content_word_count;

			// Check if it's the shortest article ever
			} elseif ( $content_word_count < $stats['shortest_post']['word_count'] ) {
				$stats['shortest_post']['post_id'] = $post->ID;
				$stats['shortest_post']['author_id'] = $post->post_author;
				$stats['shortest_post']['word_count'] = $content_word_count;
			}

			// Daily Breakdown Summary

			// Increase the article count for this day
			$stats['breakdown'][ $post_day_date ]['publish_count']++;

			// Add to the word count for this date
			$stats['breakdown'][ $post_day_date ]['total_words'] = $content_word_count;

			// Check if it's the longest article for this day
			if ( $content_word_count > $stats['breakdown'][ $post_day_date ]['longest_post']['word_count'] ) {
				$stats['breakdown'][ $post_day_date ]['longest_post']['post_id'] = $post->ID;
				$stats['breakdown'][ $post_day_date ]['longest_post']['author_id'] = $post->post_author;
				$stats['breakdown'][ $post_day_date ]['longest_post']['word_count'] = $content_word_count;

			// Check if it's the shortest article ever
			} elseif ( $content_word_count < $stats['breakdown'][ $post_day_date ]['shortest_post']['word_count'] ) {
				$stats['breakdown'][ $post_day_date ]['shortest_post']['post_id'] = $post->ID;
				$stats['breakdown'][ $post_day_date ]['shortest_post']['author_id'] = $post->post_author;
				$stats['breakdown'][ $post_day_date ]['shortest_post']['word_count'] = $content_word_count;
			}

		}

		// Work out the total average words
		$stats['average_words'] = round( $stats['total_words'] / count( $posts ) );


		// Work out the total unique authors
		$stats['author_count'] = count( array_unique( $post_authors ) );

		// Count the occurances of each author
		$authors_posts_count = array_count_values( $post_authors );

		// Flip it so we can get the author id easily
		$flipped_authors_posts_count = array_flip( $authors_posts_count );

		// Work out the most popular author
		$stats['author_popular']['count'] = max( $authors_posts_count );

		// Get the most popular author ID
		$stats['author_popular']['author_id'] = $flipped_authors_posts_count[ $stats['author_popular']['count'] ];


		// Work out the total unique categories
		$stats['category_count'] = count( array_unique( $post_categories ) );

		// Count the number of times each category is used
		$categories_posts_count = array_count_values( $post_categories );

		// Flip it so we can get the author id easily
		$flipped_categories_posts_count = array_flip( $categories_posts_count );

		// Work out the most popular author
		$stats['category_popular']['count'] = max( $categories_posts_count );

		// Get the most popular author ID
		$stats['category_popular']['category_id'] = $flipped_categories_posts_count[ $stats['category_popular']['count'] ];

		// Work out the most popular day
		$popular_date = null;
		$popular_date_count = 0;

		// Compare the value for each day against the current most popular day
		foreach ( $stats['breakdown'] as $date => $values ) {
			if ( $values['publish_count'] > $popular_date_count ) {
				$popular_date = $date;
				$popular_date_count = $values['publish_count'];
			}
		}

		$stats['date_popular']['date'] = $popular_date;
		$stats['date_popular']['count'] = $popular_date_count;

		/**
		 * Filter the post stats for the date raneg before they're returned.
		 *
		 * @since 1.0.0
		 *
		 * @var object $stats The post stats to use
		 */
		$stats = apply_filters( 'esp_post_stats', (object) $stats );

		// Cache the result
		wp_cache_add( $key, $stats, 'esp' );

		return $stats;
	}

endif;


if ( ! function_exists( 'esp_get_comment_stats' ) ) :

	/**
	 * Retrives stats for comments.
	 *
	 * Runs optimised SQL queries to get all the stats for comments.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	function esp_get_comment_stats( $date_from, $date_to ) {

		// Create a unique cache key
		$key = 'esp_comment_stats_' . md5( $date_from, $date_to );

		// See if it's cached or not
		if ( $stats = wp_cache_get( $key, 'esp' ) )
			return $stats;

		global $wpdb;

		// Holds that stats.
		// Set default values.
		$stats = array(
			'approved_comments' => 0,
			'pending_comments'  => 0,
			'posts_count'       => 0,
			'popular_post'      => array(
				'post_id' => 0,
				'count'   => 0,
			),
			'popular_user'       => array(
				'user_id' => 0,
				'count'   => 0,
			),
		);

		// Get all the content for our date range.
		$comments = $wpdb->get_results(
			$wpdb->prepare(
			"SELECT
				{$wpdb->comments}.comment_ID,
				{$wpdb->comments}.comment_post_ID,
				{$wpdb->comments}.comment_date,
				{$wpdb->comments}.comment_karma,
				{$wpdb->comments}.comment_approved,
				{$wpdb->comments}.user_id
			FROM {$wpdb->comments}
			WHERE {$wpdb->comments}.comment_date >= %s
				AND {$wpdb->comments}.comment_date < %s
			",
				$date_from,
				$date_to
			)
		);

		// No posts found, return default stats.
		if ( empty( $comments) )
			return null;

		// Used to  store all the psots comments were added to
		$comments_posts = array();

		// Loop through all the comments and work out the stats
		foreach ( $comments as $comment ){

			// If it's an approved comment increase the approved count
			if ( $comment->comment_approved ) {
				$stats['approved_comments']++;

			// If it's an unapproved comment increase the pending account
			} else {

				$stats['pending_comments']++;
			}

			$comments_posts[] = $comment->comment_post_ID;

		}

		// Count the total unique posts comments were added to
		$stats['posts_count'] = count( array_unique( $comments_posts ) );

		// Count the occurances of each post
		$comments_posts_count = array_count_values( $comments_posts );

		// Flip it so we can get the author id easily
		$flipped_comments_posts_count = array_flip( $comments_posts_count );

		// Work out the most popular author
		$stats['popular_post']['count'] = max( $comments_posts_count );

		// Get the most popular author ID
		$stats['popular_post']['post_id'] = $flipped_comments_posts_count[ $stats['popular_post']['count'] ];

		/**
		 * Filter the post stats for the date raneg before they're returned.
		 *
		 * @since 1.0.0
		 *
		 * @var object $stats The post stats to use
		 */
		$stats = apply_filters( 'esp_comment_stats', (object) $stats );

		// Cache the result
		wp_cache_add( $key, $stats, 'esp' );

		return $stats;

	}
endif;

if ( ! function_exists( 'esp_get_user_stats' ) ) :

	/**
	 * Retrives stats for users.
	 *
	 * Runs optimised SQL queries to get all the stats for users.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	function esp_get_user_stats( $date_from, $date_to ) {

		// Create a unique cache key
		$key = 'esp_user_stats_' . md5( $date_from, $date_to );

		// See if it's cached or not
		if ( $stats = wp_cache_get( $key, 'esp' ) )
			return $stats;

		global $wpdb;

		// Holds that stats.
		// Set default values.
		$stats = array(
			'active' => 0,
		);

		// Get all the content for our date range.
		$users = $wpdb->get_results(
			$wpdb->prepare(
			"SELECT
				{$wpdb->users}.ID,
				{$wpdb->users}.user_nicename
			FROM {$wpdb->users}
			WHERE {$wpdb->users}.user_registered >= %s
				AND {$wpdb->users}.user_registered < %s
			",
				$date_from,
				$date_to
			)
		);

		// No users found, return default stats.
		if ( empty( $users) )
			return null;

		// Count the new users
		$stats['active'] = count( $users );

		/**
		 * Filter the post stats for the date raneg before they're returned.
		 *
		 * @since 1.0.0
		 *
		 * @var object $stats The post stats to use
		 */
		$stats = apply_filters( 'esp_user_stats', (object) $stats );

		// Cache the result
		wp_cache_add( $key, $stats, 'esp' );

		return $stats;

	}
endif;

if ( ! function_exists( 'esp_whimsical' ) ) :

	/**
	 * Retrives whimsical stats.
	 *
	 * Runs optimised SQL queries to get whimsical stats for the end of the email.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	function esp_whimsical(  ) {

		global $wpdb;

		// @todo
		// 'most_popular_users_day_ever'
		// 'most_popular_users_month_ever'
		// 'total_tags'
		// 'most_popular_tags_ever'
		// 'single_post_tags'
		// 'post_most_comments'

		$random_stats = array(
			'first_name_stat',
			'last_name_stat',
			'most_popular_posts_date_ever',
			'most_popular_posts_month_ever',
			'most_popular_posts_day_ever',
			'post_most_revisions',
			'user_most_drafts',
		);

		$random_key = array_rand( $random_stats );

		$random_stat = $random_stats[ $random_key ];

		// // Create a unique cache key
		// $key = 'esp_whimsical_stats_' . $random_stat;
		//
		// // See if it's cached or not
		// if ( $stats = wp_cache_get( $key, 'esp' ) )
		// 	return $stats;

		if ( 'user_most_drafts' == $random_stat ) {

			$query = $wpdb->get_row("SELECT post_author, COUNT(*) AS count
					FROM {$wpdb->posts}
					WHERE post_type = 'post'
						AND post_status = 'draft'
					GROUP BY post_author
					ORDER BY count DESC
					LIMIT 1"
			);

			return sprintf(
					__( 'The user with the most drafts is %1$s with <strong>%2$s</strong> so far', 'wp-roundup' ),
					get_the_author_meta( 'display_name', $query->post_author ),
					sprintf(
						_n( '1', '%s', $query->count, 'wp-roundup' ), number_format( $query->count )
					)
				);

		}

		if ( 'post_most_revisions' == $random_stat ) {

			$query = $wpdb->get_row("SELECT post_parent, COUNT(*) AS count
					FROM {$wpdb->posts}
					WHERE post_type = 'revision'
						AND post_status = 'inherit'
					GROUP BY post_parent
					ORDER BY count DESC
					LIMIT 1"
			);

			return sprintf(
					__( 'The most revised post ever is <a href="%1$s">%2$s</a> with <strong>%3$s</strong> so far', 'wp-roundup' ),
					get_permalink( $query->post_parent ),
					get_the_title( $query->post_parent ),
					sprintf(
						_n( '1 revision', '%s revisions', $query->count, 'wp-roundup' ), number_format( $query->count )
					)
				);

		}

		if ( 'most_popular_posts_day_ever' == $random_stat ) {

			$query = $wpdb->get_row("SELECT DAYOFWEEK(post_date) day, COUNT(*) AS count
				FROM {$wpdb->posts}
				WHERE post_type = 'post'
					AND post_status = 'publish'
				GROUP BY DAYOFWEEK(post_date)
				ORDER BY count DESC
				LIMIT 1"
			);

			return sprintf(
					__( '<strong>%1$s</strong> is your most popular day of the week to post on (%2$s so far)', 'wp-roundup' ),
					date( 'l', strtotime( "Sunday + {$query->day} Days" ) ),
					sprintf(
						_n( '1 post published', '%s posts published', $query->count, 'wp-roundup' ), number_format( $query->count )
					)
				);

		}

		if ( 'most_popular_posts_date_ever' == $random_stat ) {

			$query = $wpdb->get_row("SELECT DATE(post_date) date, COUNT(*) AS count
				FROM {$wpdb->posts}
				WHERE post_type = 'post'
				AND post_status = 'publish'
				GROUP BY DATE(post_date)
				ORDER BY count DESC
				LIMIT 1"
			);

			return sprintf(
					__( '<strong>%1$s</strong> was your most popular post date EVER (%2$s)', 'wp-roundup' ),
					date( "l jS \of F Y", strtotime( $query->date ) ),
					sprintf(
						_n( '1 post published', '%s posts published', $query->count, 'wp-roundup' ), number_format( $query->count )
					)
				);

		}

		if ( 'most_popular_posts_month_ever' == $random_stat ) {

			$query = $wpdb->get_row("SELECT DATE_FORMAT(post_date,'%Y-%m') month, COUNT(*) AS count
				FROM {$wpdb->posts}
				WHERE post_type = 'post'
					AND post_status = 'publish'
				GROUP BY DATE_FORMAT(post_date,'%Y-%m')
				ORDER BY count DESC
				LIMIT 1"
			);

			return sprintf(
					__( '<strong>%1$s</strong> was your best EVER month (%2$s)', 'wp-roundup' ),
					date( "F, Y", strtotime( $query->month ) ),
					sprintf(
						_n( '1 post published', '%s posts published', $query->count, 'wp-roundup' ), number_format( $query->count )
					)
				);

		}

		if ( 'first_name_stat' == $random_stat ) {

			$first_name_occurance = $wpdb->get_row("SELECT meta_value AS first_name, COUNT(meta_value) AS count
				FROM {$wpdb->usermeta}
				WHERE meta_key = 'first_name'
				AND meta_value != ''
				GROUP BY meta_value
				ORDER BY count DESC
				LIMIT 1"
			);

			return sprintf(
					__( '<strong>%1$s</strong> is the most popular first name on your site, used by %2$s', 'wp-roundup' ),
					$first_name_occurance->first_name,
					sprintf(
						_n( '1 user', '%s users', $first_name_occurance->count, 'wp-roundup' ), number_format( $first_name_occurance->count )
					)
				);

		}

		if ( 'last_name_stat' == $random_stat ) {

			$last_name_occurance = $wpdb->get_row("SELECT meta_value AS last_name, COUNT(meta_value) AS count
				FROM {$wpdb->usermeta}
				WHERE meta_key = 'last_name'
				AND meta_value != ''
				GROUP BY meta_value
				ORDER BY count DESC
				LIMIT 1"
			);

			return sprintf(
					__( '<strong>%1$s</strong> is the most popular last name on your site, used by %2$s', 'wp-roundup' ),
					$last_name_occurance->last_name,
					sprintf(
						_n( '1 user', '%s users', $last_name_occurance->count, 'wp-roundup' ), number_format( $last_name_occurance->count )
					)
				);

		}


	}
endif;
