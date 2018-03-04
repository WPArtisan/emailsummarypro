<?php
/**
 * Register the default template part.
 *
 * @package     email-summary-pro
 * @subpackage  Includes
 * @copyright   Copyright (c) 2017, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * [esp_setup_default_email_template_parts description]
 * @return [type] [description]
 */
function esp_setup_default_email_template_parts() {

	// All the default HTML email parts.
	$template_parts = array(
		array(
			'order'    => 5,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'pre-content',
			'callback' => null,
		),
		array(
			'order'    => 10,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'logo',
			'callback' => null,
		),
		array(
			'order'    => 15,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'branding',
			'callback' => null,
		),
		array(
			'order'    => 20,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'introduction',
			'callback' => null,
		),
		array(
			'order'    => 25,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'posts',
			'callback' => 'esp_get_post_stats',
		),
		array(
			'order'    => 30,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'data-table',
			'callback' => null,
		),
		array(
			'order'    => 35,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'comments',
			'callback' => null,
		),
		array(
			'order'    => 40,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'users',
			'callback' => null,
		),
		array(
			'order'    => 45,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'updates',
			'callback' => null,
		),
		array(
			'order'    => 50,
			'method'   => 'email',
			'type'     => 'html',
			'part'     => 'signoff',
			'callback' => null,
		),

		// All the default plain email parts.
		array(
			'order'    => 5,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'introduction',
			'callback' => null,
		),
		array(
			'order'    => 10,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'posts',
			'callback' => null,
		),
		array(
			'order'    => 15,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'comments',
			'callback' => null,
		),
		array(
			'order'    => 20,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'users',
			'callback' => null,
		),
		array(
			'order'    => 25,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'updates',
			'callback' => null,
		),
		array(
			'order'    => 30,
			'method'   => 'email',
			'type'     => 'plain',
			'part'     => 'signoff',
			'callback' => null,
		),
	);

	foreach ( $template_parts as $part ) {
		esp_add_template_part( $part['method'], $part['type'], $part['part'], $part['order'], $part['callback'] );
	}
}
add_action( 'plugins_loaded', 'esp_setup_default_email_template_parts' );
