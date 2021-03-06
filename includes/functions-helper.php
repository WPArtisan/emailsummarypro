<?php

/**
 * General helper functions for the plugin
 *
 * @author OzTheGreat
 * @since  1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'esp_get_option' ) ) :

	/**
	 * Retrieves a single plugin option.
	 *
	 * Gets a single option from the global array, runs it through
	 * filters then returns it. The second param can set a default value to be
	 * returned if the option doesn't exist
	 *
	 * @since 1.0.0
	 *
	 * @global $esp_options   Global array holding the plugin options.
	 *
	 * @param  string $name    The name of the option to retrieve.
	 * @param  mixed  $default Optional. The default value to return.
	 *                         Default false.
	 * @return mixed The option or default value.
	 */
	function esp_get_option( $name, $default = false ) {
		// Grab all the options.
		$esp_options = esp_get_options();

		// Setup the default value
		$value = $default;

		// Check if it exists in the global options array
		if ( ! empty( $esp_options[ $name ] ) )
			$value = $esp_options[ $name ];

		/**
		 * Filter all the option values before they're returned
		 *
		 * @since 1.0.0
		 *
		 * @param mixed  $value   The value being returned.
		 * @param string $name    The name of the option being retrieved.
		 * @param mixed  $default The default value to return.
		 */
		$option = apply_filters( 'esp_get_option', $value, $name, $default );

		/**
		 * Filter a specific option value before it's returned
		 *
		 * @since 1.0.0
		 *
		 * @param mixed  $value   The value being returned.
		 * @param string $name    The name of the option being retrieved.
		 * @param mixed  $default The default value to return.
		 */
		$option = apply_filters( 'esp_get_option_' . $name, $value, $name, $default );

		return $option;
	}

endif;


if ( ! function_exists( 'esp_get_options' ) ) :

	/**
	 * Retrieves all plugin options.
	 *
	 * Gets all options from the global array, runs them through
	 * filters then returns them.
	 *
	 * @since 1.0.0
	 *
	 * @global $esp_options Global array holding the plugin options.
	 *
	 * @return array All of the plugin's options.
	 */
	function esp_get_options() {
		// get_option is cached so can call it as much as we like.
		$esp_options = get_option( 'esp_options' );

		/**
		 * Filter all the option values before they're returned
		 *
		 * @since 1.0.0
		 *
		 * @param array $esp_options The options being returned.
		 */
		$esp_options = apply_filters( 'esp_get_options', $esp_options );

		return $esp_options;
	}

endif;

if ( ! function_exists( 'boolval' ) ) :

	/**
	 * Converts a value to a boolean.
	 *
	 * PHP <= 5.5 didn't have boolval function, this patches it in.
	 *
	 * @param  mixed $value
	 * @return boolean
	 */
	function boolval( $value ) {
		return (bool) $value;
	}

endif;

if ( ! function_exists( 'str_word_count' ) ) :

	/**
	 * Count words in a string.
	 *
	 * str_word_count was introduced in PHP 5.2.11, this patches it for lesser
	 * versions. This is a simplicatic version but we're not really expecting
	 * anyone to use it.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	function str_word_count( $string ) {
		return count( explode( " ", $string ) );
	}
endif;

if ( ! function_exists( 'esp_load_textdomain' ) ) :

	/**
	 * Load plugin textdomain.
	 *
	 * Checks in the languages folder by default.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	function esp_load_textdomain() {
		load_plugin_textdomain( 'wp-roundup', false, WP_ROUNDUP_BASE_PATH . '/languages' );
	}
endif;

if ( ! function_exists( 'esp_circle' ) ) :

	/**
	 * Generates a CSS circle that can be used in emails
	 *
	 * Email clients don't really like CSS3 so this is a special version
	 *
	 * @since 1.0.0
	 *
	 * @param int $percentage In pixels
	 * @param string $color Hex value
	 * @return int
	 */
	function esp_bar_line( $percentage, $color = '#EB4102' ) {
		// The max size is out of a 100
		// We don't want it getting to large so trim it down to out of 25
		$percentage = $percentage * 2;

		// Make sure color has a preceeding hash
		$color = '#' . ltrim( $color, '#' );
		?>
			<!--[if mso]>
				<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:<?php echo absint( $percentage ); ?>px;v-text-anchor:middle;width:4px;vertical-align:bottom;" arcsize="600%" stroke="f" fillcolor="<?php echo $color; ?>">
				<w:anchorlock/>
				<center>
			<![endif]-->
			<a style="background-color:<?php echo $color; ?>;border-radius:600px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:<?php echo absint( $percentage ); ?>px;text-align:center;text-decoration:none;width:4px;-webkit-text-size-adjust:none;vertical-align:bottom;">&nbsp;</a>
			<!--[if mso]>
				</center>
				</v:roundrect>
			<![endif]-->
		<?php
	}
endif;
