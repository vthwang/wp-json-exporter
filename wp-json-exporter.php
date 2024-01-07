<?php
/**
 * Plugin Name:       WordPress JSON Exporter
 * Plugin URI:        https://github.com/vthwang/wp-json-exporter
 * Description:       Customized for blog systems, this plugin enables the export of your WordPress posts and projects in JSON format.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Text Domain:       wp-json-exporter
 * Author:            Vincent Wang
 * Author URI:        https://vthwang.com
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPJsonExporter' ) ) {
	define( 'WP_JSON_EXPORTER_VERSION', '1.0.0' );
	define( 'WP_JSON_EXPORTER_DIR', __DIR__ );

	class WPJsonExporter {
		public function __construct() {
			$this->register_autoload();
			$this->register_admin_page();
			$this->register_api();
		}

		private function register_autoload(): void {
			spl_autoload_register( function ( $name ) {
				$name = strtolower( $name );
				$name = str_replace( '_', '-', $name );
				$name = 'class-' . $name;
				$file = __DIR__ . '/admin/classes/' . $name . '.php';

				if ( file_exists( $file ) ) {
					require_once $file;
				}
			} );
		}

		public function register_admin_page(): void {
			new WPJsonExporterAdminPage();
		}

		public function register_api(): void {
			new WPJsonExporterAPI();
		}
	}

	new WPJsonExporter();
}

if ( class_exists( 'WPJsonExporterActivation' ) ) {
	register_activation_hook( __FILE__, array( 'WPJsonExporterActivation', 'activate' ) );
}

if ( class_exists( 'WPJsonExporterDeactivation' ) ) {
	register_deactivation_hook( __FILE__, array( 'WPJsonExporterDeactivation', 'deactivate' ) );
}

if ( class_exists( 'WPJsonExporterUninstall' ) ) {
	register_uninstall_hook( __FILE__, array( 'WPJsonExporterUninstall', 'uninstall' ) );
}