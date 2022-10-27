<?php
/**
 * ThemeZee Icon Heading Block License Settings
 *
 * @package ThemeZee Icon Heading Block
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ThemeZee_Icon_Heading_Block_License_Settings {

	/**
	 * Setup the ThemeZee Plugins Settings class
	 */
	static function setup() {

		// Register settings.
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );

		// Add License API functions.
		add_action( 'admin_init', array( __CLASS__, 'activate_license' ) );
		add_action( 'admin_init', array( __CLASS__, 'deactivate_license' ) );

		// Add Admin notices.
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
	}

	/**
	 * Get settings
	 *
	 * @return array
	 */
	static function get_settings() {
		$default_settings = array(
			'icon_heading_block_license_key'    => '',
			'icon_heading_block_license_status' => 'inactive',
		);

		return wp_parse_args( get_option( 'themezee_blocks_settings', array() ), $default_settings );
	}

	/**
	 * Register all settings sections and fields
	 */
	static function register_settings() {

		// Add License Key Setting.
		add_settings_field(
			'themezee_blocks_settings[icon_heading_block_license_key]',
			esc_html__( 'Icon Heading Block', 'themezee-icon-heading-block' ),
			array( __CLASS__, 'render_license_key_setting' ),
			'themezee_blocks_settings',
			'themezee_blocks_license_section'
		);
	}

	/**
	 * License Key Callback
	 */
	static function render_license_key_setting() {
		$options        = self::get_settings();
		$license_status = $options['icon_heading_block_license_status'];
		$license_key    = ! empty( $options['icon_heading_block_license_key'] ) ? trim( $options['icon_heading_block_license_key'] ) : false;

		$html = '';
		if ( 'valid' === $license_status && ! empty( $license_key ) ) {
			$html .= '<span style="display: inline-block; width: 25em; margin: 0 1px; padding: 0 8px;line-height: 2;border-radius: 4px;border: 1px solid #8c8f94;">*************************' . esc_html( substr( stripslashes( $license_key ), 25 ) ) . '</span>';
			$html .= '<input type="submit" class="button" name="themezee_icon_heading_block_deactivate_license" value="' . esc_attr__( 'Deactivate License', 'themezee-icon-heading-block' ) . '"/>';
			$html .= '<br/><span class="description">' . esc_html__( 'Active. You are receiving updates.', 'themezee-icon-heading-block' ) . '</span>';
		} else {
			$html .= '<input type="text" class="regular-text" id="themezee_blocks_settings[icon_heading_block_license_key]" name="themezee_blocks_settings[icon_heading_block_license_key]" value="' . esc_attr( stripslashes( $license_key ) ) . '"/>';
			$html .= '<input type="submit" class="button" name="themezee_icon_heading_block_activate_license" value="' . esc_attr__( 'Activate License', 'themezee-icon-heading-block' ) . '"/>';
			$html .= '<br/><span class="description">' . esc_html__( 'Inactive. Please enter your license key.', 'themezee-icon-heading-block' ) . '</span>';
		}

		echo $html;
		wp_nonce_field( 'themezee_icon_heading_block_license_nonce', 'themezee_icon_heading_block_license_nonce' );
	}

	/**
	 * Activates the license key.
	 */
	static function activate_license() {

		// Return early if not on ThemeZee Blocks page.
		if ( ! isset( $_POST['themezee_blocks_settings'] ) ) {
			return;
		}

		// Listen for our activate button to be clicked.
		if ( ! isset( $_POST['themezee_icon_heading_block_activate_license'] ) ) {
			return;
		}

		// Run a quick security check.
		if ( ! check_admin_referer( 'themezee_icon_heading_block_license_nonce', 'themezee_icon_heading_block_license_nonce' ) ) {
			return;
		}

		// Get License key.
		$license_key = ! empty( $_POST['themezee_blocks_settings']['icon_heading_block_license_key'] ) ? sanitize_text_field( $_POST['themezee_blocks_settings']['icon_heading_block_license_key'] ) : false;

		// Return if no license key was entered.
		if ( ! $license_key ) {
			return;
		}

		// Data to send in our API request.
		$api_params = array(
			'edd_action'  => 'activate_license',
			'license'     => $license_key,
			'item_id'     => THEMEZEE_ICON_HEADING_BLOCK_ID,
			'item_name'   => rawurlencode( THEMEZEE_ICON_HEADING_BLOCK_NAME ),
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			THEMEZEE_ICON_HEADING_BLOCK_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => $api_params,
			)
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

					case 'expired':
						$message = sprintf(
							/* translators: the license key expiration date */
							__( 'Your license key expired on %s.', 'themezee-icon-heading-block' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'disabled':
					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'themezee-icon-heading-block' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'themezee-icon-heading-block' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'themezee-icon-heading-block' );
						break;

					case 'item_name_mismatch':
						/* translators: the plugin name */
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'themezee-icon-heading-block' ), THEMEZEE_ICON_HEADING_BLOCK_NAME );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'themezee-icon-heading-block' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'themezee-icon-heading-block' );
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure.
		if ( ! empty( $message ) ) {
			$redirect = add_query_arg(
				array(
					'page'                  => 'themezee-blocks',
					'icon_heading_block_activation' => 'false',
					'message'               => rawurlencode( $message ),
				),
				admin_url( 'options-general.php' )
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// Retrieve the license from the database.
		$options = self::get_settings();

		// $license_data->license will be either "valid" or "invalid".
		if ( 'valid' === $license_data->license ) {
			$options['icon_heading_block_license_key'] = $license_key;
		}
		$options['icon_heading_block_license_status'] = $license_data->license;
		update_option( 'themezee_blocks_settings', $options );

		wp_safe_redirect( admin_url( 'options-general.php?page=themezee-blocks' ) );
		exit();
	}

	/**
	 * Deactivates the license key.
	 * This will decrease the site count.
	 */
	static function deactivate_license() {

		// Listen for our activate button to be clicked.
		if ( ! isset( $_POST['themezee_icon_heading_block_deactivate_license'] ) ) {
			return;
		}

		// Run a quick security check.
		if ( ! check_admin_referer( 'themezee_icon_heading_block_license_nonce', 'themezee_icon_heading_block_license_nonce' ) ) {
			return;
		}

		// Retrieve our license key from the DB.
		$options     = self::get_settings();
		$license_key = ! empty( $options['icon_heading_block_license_key'] ) ? trim( $options['icon_heading_block_license_key'] ) : false;

		// data to send in our API request
		$api_params = array(
			'edd_action'  => 'deactivate_license',
			'license'     => $license_key,
			'item_id'     => THEMEZEE_ICON_HEADING_BLOCK_ID,
			'item_name'   => rawurlencode( THEMEZEE_ICON_HEADING_BLOCK_NAME ), // the name of our product in EDD
			'url'         => home_url(),
			'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
		);

		// Call the custom API.
		$response = wp_remote_post(
			THEMEZEE_ICON_HEADING_BLOCK_STORE_URL,
			array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => $api_params,
			)
		);

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}

			$redirect = add_query_arg(
				array(
					'page'                  => 'themezee-blocks',
					'icon_heading_block_activation' => 'false',
					'message'               => rawurlencode( $message ),
				),
				admin_url( 'options-general.php' )
			);

			wp_safe_redirect( $redirect );
			exit();
		}

		// Decode the license data.
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' === $license_data->license ) {
			$options['icon_heading_block_license_key']    = '';
			$options['icon_heading_block_license_status'] = $license_data->license;
			update_option( 'themezee_blocks_settings', $options );
		}

		wp_safe_redirect( admin_url( 'options-general.php?page=themezee-blocks' ) );
		exit();
	}

	/**
	 * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	static function admin_notices() {
		if ( isset( $_GET['icon_heading_block_activation'] ) && ! empty( $_GET['message'] ) ) {

			switch ( $_GET['icon_heading_block_activation'] ) {

				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo wp_kses_post( $message ); ?></p>
					</div>
					<?php
					break;

				case 'true':
				default:
					// Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}
}

// Run class.
ThemeZee_Icon_Heading_Block_License_Settings::setup();
