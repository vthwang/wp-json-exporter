<?php
/**
 * File for class WP_Json_Exporter_Activation.
 *
 * This file contains the WP_Json_Exporter_Activation class
 * which handles the activation procedures of the plugin.
 *
 * @package WP_JSON_Exporter
 */

if ( ! class_exists( 'WP_Json_Exporter_Activation' ) ) {
	/**
	 * Class WP_Json_Exporter_Activation
	 *
	 * This class handles the activation procedures of the plugin.
	 */
	class WP_Json_Exporter_Activation {
		/**
		 * Activate the plugin.
		 *
		 * This method handles the activation setup for the plugin, such as
		 * adding version and setting default options.
		 */
		public static function activate(): void {
			/** Add version */
			add_option( 'wp_json_exporter_version', WP_JSON_EXPORTER_VERSION );
			/** Add settings fields */
			add_option( 'wp_json_exporter_is_redirect', 0 );
			add_option( 'wp_json_exporter_redirect_url', '' );
		}
	}
}
