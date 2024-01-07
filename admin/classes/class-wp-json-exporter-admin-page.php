<?php

if ( ! class_exists( 'WPJsonExporterAdminPage' ) ) {
	class WPJsonExporterAdminPage {
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		}
	}
}