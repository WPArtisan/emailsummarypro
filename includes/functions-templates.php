<?php
/**
 * Functions for managing template parts.
 *
 * @package     email-summary-pro
 * @subpackage  Includes
 * @copyright   Copyright (c) 2018, WPArtisan
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'esp_add_template_part' ) ) :

	/**
	 * Add a template part into the template manager.
	 *
	 * @param string  $method        Method used to send the summary.
	 * @param string  $template      Template to use.
	 * @param string  $part          Template part to add.
	 * @param integer $order         Order to add the template part to the template.
	 * @param string|array $callback Used to supply args to the template.
	 * @return void
	 */
	function esp_add_template_part( $method, $template, $part, $order = 50, $callback = null ) {
		global $esp_templates;

		if ( ! $esp_templates || ! is_array( $esp_templates ) ) {
			$esp_templates = array();
		}

		$esp_templates[ $method ][ $template ][ $order ][ $part ] = $callback;
	}
endif;

if ( ! function_exists( 'esp_get_template_part' ) ) :

	/**
	 * Return a template part with populated placeholders.
	 *
	 * @param  object $summary  Email_Summary_Pro_Summary
	 * @param  string $template The template to search for.
	 * @param  string $part     The template part to load.
	 * @return string
	 */
	function esp_get_template_part( $summary, $template, $part ) {
		global $esp_templates;

		$method = $summary->method;

		// Default to email.
		if ( ! empty( $summary->method ) ) {
			$method = 'email';
		}

		// Check the template exists before trying to get it
		if ( ! file_exists( esp_locate_template( $method . '/' . $template . '/' . $part ) ) ) {
			return false;
		}

		// Default arguments used in all templates.
		$arguments = array(
			'site_name'        => get_bloginfo( 'name' ),
			'site_description' => get_bloginfo( 'description' ),
			'site_url'         => get_bloginfo( 'url' ),
			'blog_id'          => get_current_blog_id(),
			'date'             => $summary->date, // The roundup is sent the day after the week ends.
			'date_from'        => $summary->date_from, // The first date of the week we're rounding up.
			'date_to'          => $summary->date_to, // The last date of the week we're rounding up (inclusive).
		);

		/**
		 * Filter specific arguments used in all templates.
		 *
		 * @var array $arguments key => value array of default arguments.
		 * @var string $method   Current method using to send the summary.
		 * @var string $template Type of message to send.
		 * @var string $part     Template part to load.
		 */
		$arguments = apply_filters( 'esp_template_part_default_arguments', $arguments, $method, $template, $part );

		// Template specific arguments.
		if ( isset( $esp_templates[ $method ][ $template ][ $part ] ) ) {

			// The function should return a key => value array of argument => value.
			$template_part_arguments = call_user_func( $esp_templates[ $method ][ $template ][ $part ] );

			// Merge them into the default arguments.
			$arguments = array_merge( $arguments, $template_part_arguments );
		}

		/**
		 * Filter specific arguments used in this template.
		 *
		 * @var array $arguments key => value array of template specific arguments.
		 */
		$arguments = apply_filters( 'esp_template_part_arguments-' . $method . '-' . $template . '-' . $part, $arguments );

		extract( $arguments );

		ob_start();

		include esp_locate_template( $method . '/' . $template . '/' . $part );

		// Read the contents into a variable.
		$template_content = ob_get_contents();

		// Turn off output buffering.
		ob_end_clean();

		// Replace all the arguments.
		foreach ( $arguments as $key => $value ) {
			$template_content = str_replace( sprintf( '%%%s%%', $key ), $value, $template_content );
		}

		/**
		 * Filter specific arguments used in this template.
		 *
		 * @var string $template_content The content of the tempate.
		 * @var array  $arguments        key => value array of template specific arguments.
		 */
		$template_content = apply_filters( 'esp_template_part-' . $method . '-' . $template . '-' . $part, $template_content, $arguments );

		return $template_content;
	}
endif;

if ( ! function_exists( 'esp_get_template_parts' ) ) :

	/**
	 * Retrieve all the parts for a template.
	 *
	 * @param  string $method   template for method
	 * @param  string $template Template name.
	 * @return array  part_name => callback for arguments
	 */
	function esp_get_template_parts( $method, $template ) {
		global $esp_templates;

		if ( ! isset( $esp_templates[ $method ][ $template ] ) ) {
			return null;
		}

		$template_parts = $esp_templates[ $method ][ $template ];

		// Just in case.
		ksort( $template_parts );

		/**
		 * Filter template parts for this method and template.
		 *
		 * @var string $template_parts array part_name => callback for arguments.
		 */
		$template_parts = apply_filters( 'esp_template_parts-' . $method . '-' . $template, $template_parts );

		return $template_parts;
	}
endif;

if ( ! function_exists( 'esp_get_template' ) ) :

	/**
	 * Get an entire template for a summary
	 *
	 * @param  onject $summary  Summary to load the template for.
	 * @param  string $template Load a specifc template.
	 * @return string Template content.
	 */
	function esp_get_template( $summary, $template = null ) {
		if ( is_null( $template ) ) {
			$template = $summary->template;
		}

		// Get all the registered template parts.
		$template_parts = esp_get_template_parts( $summary->method, $template );

		$content = '';

		// Cycle through them all and string them together.
		foreach ( $template_parts as $order => $parts ) {
			foreach ( $parts as $part => $callback ) {
				$content .= esp_get_template_part( $summary, $template, $part );
			}
		}

		return $content;
	}
endif;

if ( ! function_exists( 'esp_locate_template' ) ) :

	/**
	 * Locates a plugin template and returns the path to it
	 *
	 * Takes a template name and first searches for it in themes to see if
	 * it's been overridden or not. If it can't find it defaults to the one
	 * located in the plugin.
	 *
	 * @since 1.0.0
	 * @todo Pass params through?
	 *
	 * @param  string $name Name of the template to locate.
	 * @return string The full path to the template file.
	 */
	function esp_locate_template( $name ) {

		// Check if there's an extension or not
		$name .= '.php' !== substr( $name, -4 ) ? '.php' : '' ;

		// locate_template() returns the path to file
		// if either the child theme or the parent theme have overridden the template
		if ( $overridden_template = locate_template( 'wp-roundup/' . $name ) )
			return $overridden_template;

		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' sub-directory of the directory this file is in
		$template_path = ESP_PLUGIN_DIR . '/templates/' . $name;

		/**
		 * Alter the path for a template file
		 *
		 * @since 1.0.0
		 *
		 * @param string $template_path The path to the template.
		 * @param string $name          The name of the template to locate.
		 */
		$template_path = apply_filters( 'esp_template_path', $template_path, $name );

		return $template_path;
	}

endif;
