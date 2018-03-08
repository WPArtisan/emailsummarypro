<?php

/**
 * When the plugin is first activated, setup the default options.
 *
 * @return void
 */
function esp_install() {

	// If this option already exists then the
	// install function has run before.
	if ( get_option( 'esp_db_version' ) ) {
		return;
	}

	// This runs before the plugin is loaded so ensure
	// any custom post_types & custom post_type_statuses registered.
	esp_setup_summaries_post_type();
	esp_register_post_type_statuses();

	// Create a default summary.
	$defaul_summary = array(
		'name'       => esc_html__( 'Weekly Site Summary', 'email-summary-pro' ),
		'recipients' => get_bloginfo( 'admin_email' ),
		'subject'    => esc_html__( 'Weekly Summary', 'email-summary-pro' ),
		'interval'   => 'weekly',
	);

	// Add the default summary in.
	esp_add_summary( $defaul_summary );

	// They may deactivate / re-activate. Let's try not to be annoying.
	if ( ! get_option( 'esp_activation_time' ) ) {
		add_option( 'esp_activation_time', date( 'c' ) );
		// When to provide prompts for plugin ratings, in days.
		add_option( 'esp_rating_prompts', array( 7, 30, 90 ) );
	}

	// Set the current DB version.
	update_option( 'esp_db_version', ESP_VERSION );
}
register_activation_hook( ESP_PLUGIN_FILE, 'esp_install' );
