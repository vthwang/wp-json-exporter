<?php
/**
 * Plugin Name:       WordPress JSON Exporter
 * Plugin URI:        https://github.com/vthwang/wp-json-exporter
 * Description:       Customized for blog systems, this plugin enables the export of your WordPress posts and projects in JSON format.
 * Version:           1.0.4
 * Requires at least: 6.5
 * Text Domain:       wp-json-exporter
 * Author:            Vincent Wang
 * Author URI:        https://vthwang.com
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WP_JSON_Exporter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJsonExporter' ) ) {
	define( 'WP_JSON_EXPORTER_VERSION', '1.0.4' );
	define( 'WP_JSON_EXPORTER_DIR', __DIR__ );
	define( 'WP_JSON_EXPORTER_VISITS_TABLE', 'json_exporter_visits' );

	/**
	 * Class WP_Json_Exporter
	 *
	 * This class handles the main plugin procedures.
	 */
	class WP_Json_Exporter {
		/**
		 * WP_Json_Exporter constructor.
		 *
		 * This method handles the initialization of the plugin.
		 */
		public function __construct() {
			$this->register_autoload();
			$this->register_admin_page();
			$this->register_custom_post_type();
			$this->register_api();
		}

		/**
		 * Register the autoloader.
		 *
		 * This method registers the autoloader for the plugin.
		 */
		public function register_autoload(): void {
			spl_autoload_register(
				function ( $name ) {
					$name = strtolower( $name );
					$name = str_replace( '_', '-', $name );
					$name = 'class-' . $name;
					$file = __DIR__ . '/admin/classes/' . $name . '.php';

					if ( file_exists( $file ) ) {
						require_once $file;
					}
				}
			);
		}

		/**
		 * Register the admin page.
		 *
		 * This method registers the admin page for the plugin.
		 */
		public function register_admin_page(): void {
			new WP_Json_Exporter_Admin_Page();
		}

		/**
		 * Register the custom post type.
		 *
		 * This method registers the custom post type for the plugin.
		 */
		public function register_custom_post_type(): void {
			new WP_Json_Exporter_Custom_Post_Type();
		}

		/**
		 * Register the API.
		 *
		 * This method registers the API for the plugin.
		 */
		public function register_api(): void {
			new WP_Json_Exporter_API();
		}
	}

	new WP_Json_Exporter();
}

if ( class_exists( 'WP_Json_Exporter_Activation' ) ) {
	register_activation_hook( __FILE__, array( 'WP_Json_Exporter_Activation', 'activate' ) );
}

if ( class_exists( 'WP_Json_Exporter_Deactivation' ) ) {
	register_deactivation_hook( __FILE__, array( 'WP_Json_Exporter_Deactivation', 'deactivate' ) );
}

if ( class_exists( 'WP_Json_Exporter_Uninstall' ) ) {
	register_uninstall_hook( __FILE__, array( 'WP_Json_Exporter_Uninstall', 'uninstall' ) );
}
