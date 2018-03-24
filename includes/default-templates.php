<?php
/**
 * Register the default template part.
 *
 * @package     email-summary-pro
 * @subpackage  Includes
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Register all the default template parts for email/html.
 *
 * @return void
 */
function esp_setup_default_email_template_parts() {

	// All the default HTML email parts.
	$template_parts = array(
		array(
			'order'    => 1,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'header',
		),
		array(
			'order'    => 5,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'pre-content',
		),
		array(
			'order'    => 10,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'content-start',
		),
		array(
			'order'    => 15,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'logo',
		),
		array(
			'order'    => 25,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'introduction',
		),
		array(
			'order'    => 30,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'posts',
		),
		array(
			'order'    => 35,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'data-table',
		),
		array(
			'order'    => 40,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'comments',
		),
		array(
			'order'    => 45,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'users',
		),
		array(
			'order'    => 50,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'updates',
		),
		array(
			'order'    => 75,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'content-end',
		),
		array(
			'order'    => 100,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'signoff',
		),
		array(
			'order'    => 500,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'footer',
		),

		// All the default plain email parts.
		array(
			'order'    => 5,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'introduction',
		),
		array(
			'order'    => 10,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'posts',
		),
		array(
			'order'    => 15,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'comments',
		),
		array(
			'order'    => 20,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'users',
		),
		array(
			'order'    => 25,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'updates',
		),
		array(
			'order'    => 30,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'signoff',
		),
	);

	foreach ( $template_parts as $part ) {
		esp_add_template_part( $part['method'], $part['type'], $part['part'], $part['order'] );
	}
}
add_action( 'plugins_loaded', 'esp_setup_default_email_template_parts' );
