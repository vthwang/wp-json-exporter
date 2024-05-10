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
			/** Create visits table */
			self::create_visits_table();
		}

		private static function create_visits_table(): void {
			global $wpdb;
			$table_name      = $wpdb->prefix . WP_JSON_EXPORTER_VISITS_TABLE;
			$charset_collate = $wpdb->get_charset_collate();
			$sql             = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
		        route varchar(255) NOT NULL,
		        count bigint(20) DEFAULT 0 NOT NULL,
		        createdAt datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		        updatedAt datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
		        PRIMARY KEY  (id),
		        UNIQUE KEY route (route)
			) $charset_collate;";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}
}
