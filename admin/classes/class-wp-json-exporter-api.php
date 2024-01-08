<?php

if ( ! class_exists( 'WP_Json_Exporter_API' ) ) {
	class WP_Json_Exporter_API {
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_api' ) );
		}
	}
}