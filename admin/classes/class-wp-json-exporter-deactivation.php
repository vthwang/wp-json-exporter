<?php
/**
 * File for class WP_Json_Exporter_Deactivation.
 *
 * @package WP_JSON_Exporter
 */

if ( ! class_exists( 'WP_Json_Exporter_Deactivation' ) ) {
	/**
	 * Class WP_Json_Exporter_Deactivation
	 *
	 * This class handles the deactivation procedures of the plugin.
	 */
	class WP_Json_Exporter_Deactivation {
		/**
		 * Deactivate the plugin.
		 *
		 * This method handles the deactivation cleanup for the plugin.
		 */
		public static function deactivate(): void {
			// Do nothing.
		}
	}
}
