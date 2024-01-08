<?php

if ( ! class_exists( 'WP_Json_Exporter_Admin_Page' ) ) {
	class WP_Json_Exporter_Admin_Page {
		function __construct() {
			add_action( 'admin_init', array( $this, 'wp_json_exporter_settings_init' ) );
			add_action( 'admin_menu', array( $this, 'add_menu' ) );
            add_action('template_redirect', array($this, 'check_redirect'));
		}

		function wp_json_exporter_settings_init(): void {
			register_setting( 'wp-json-exporter-settings', 'wp_json_exporter_is_redirect' );
			register_setting( 'wp-json-exporter-settings', 'wp_json_exporter_redirect_url' );
		}

		function add_menu(): void {
			add_menu_page(
				__( 'WP JSON Exporter', 'wp-json-exporter' ),
				__( 'WP JSON Exporter', 'wp-json-exporter' ),
				'manage_options',
				'wp-json-exporter',
				array( $this, 'wp_json_exporter_settings_page' ),
				'dashicons-rest-api',
				100
			);
		}

		function wp_json_exporter_settings_page(): void {
			?>
            <div class="wrap">
                <h2>WordPress JSON Exporter Settings</h2>

                <form method="post" action="options.php">
					<?php settings_fields( 'wp-json-exporter-settings' ); ?>
					<?php do_settings_sections( 'wp-json-exporter-settings' ); ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row">Is Redirect:</th>
                            <td><input type="checkbox"
                                       name="wp_json_exporter_is_redirect" <?php checked( get_option( 'wp_json_exporter_is_redirect' ), 1 ); ?>
                                       value="1"></td>
                        </tr>
                        <tr>
                            <th scope="row">Redirect URL:</th>
                            <td><input type="text" name="wp_json_exporter_redirect_url"
                                       value="<?php echo esc_attr( get_option( 'wp_json_exporter_redirect_url' ) ); ?>">
                            </td>
                        </tr>
                    </table>

					<?php submit_button(); ?>
                </form>
            </div>
			<?php
		}

        function check_redirect(): void {
	        if (get_option('wp_json_exporter_is_redirect') == '1') {
		        $redirect_url = get_option('wp_json_exporter_redirect_url');

		        // If the redirect URL is not empty, perform the redirect
		        if (!empty($redirect_url)) {
			        wp_redirect($redirect_url);
			        exit;
		        }
	        }
        }
	}
}