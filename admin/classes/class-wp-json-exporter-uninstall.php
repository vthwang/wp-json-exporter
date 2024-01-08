<?php

if ( ! class_exists( 'WP_Json_Exporter_Uninstall' ) ) {
	class WP_Json_Exporter_Uninstall {
		static function uninstall(): void {
			// remove options
			delete_option( 'wp_json_exporter_version' );
			delete_option( 'wp_json_exporter_is_redirect' );
			delete_option( 'wp_json_exporter_redirect_url' );
		}
	}
}