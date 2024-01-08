<?php

if ( ! class_exists( 'WP_Json_Exporter_Activation' ) ) {
	class WP_Json_Exporter_Activation {
		public static function activate(): void {
			// add version
			add_option( 'wp_json_exporter_version', WP_JSON_EXPORTER_VERSION );
			// add settings fields
			add_option( 'wp_json_exporter_is_redirect', 0 );
			add_option( 'wp_json_exporter_redirect_url', '' );
		}
	}
}