<?php

if ( ! class_exists( 'WPJsonExporterAPI' ) ) {
	class WPJsonExporterAPI {
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_api' ) );
		}
	}
}