<?php
/**
 * ThemeZee Blocks Admin Page
 *
 * @package ThemeZee Icon Heading Block
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Use class to avoid namespace collisions.
if ( ! class_exists( 'ThemeZee_Blocks_Admin_Page' ) ) :

	class ThemeZee_Blocks_Admin_Page {

		/**
		 * Setup the ThemeZee Plugins Settings class
		 *
		 * @return void
		 */
		static function setup() {

			// Add settings page.
			add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ), 9 );

			// Register settings.
			add_action( 'admin_init', array( __CLASS__, 'register_settings' ), 9 );
		}

		/**
		 * Add Settings Page to Admin menu
		 */
		static function add_settings_page() {
			add_options_page(
				esc_html__( 'ThemeZee Blocks', 'themezee-icon-heading-block' ),
				esc_html__( 'ThemeZee Blocks', 'themezee-icon-heading-block' ),
				'manage_options',
				'themezee-blocks',
				array( __CLASS__, 'render_settings_page' )
			);
		}

		/**
		 * Display settings page
		 */
		static function render_settings_page() {
			ob_start();
			?>

			<div class="wrap">

				<h1><?php esc_html_e( 'ThemeZee Blocks', 'themezee-icon-heading-block' ); ?></h1>

				<form method="post" action="options.php">
					<?php
						do_settings_sections( 'themezee_blocks_settings' );
						settings_fields( 'themezee_blocks_settings' );
						submit_button();
					?>
				</form>

			</div>

			<?php
			echo ob_get_clean();
		}

		/**
		 * Register all settings sections and fields
		 */
		static function register_settings() {

			// Make sure that options exist in database.
			if ( false === get_option( 'themezee_blocks_settings' ) ) {
				add_option( 'themezee_blocks_settings' );
			}

			// Add Section.
			add_settings_section(
				'themezee_blocks_license_section',
				esc_html__( 'License Settings', 'themezee-icon-heading-block' ),
				array( __CLASS__, 'license_section_intro' ),
				'themezee_blocks_settings'
			);

			// Creates our settings in the options table.
			register_setting(
				'themezee_blocks_settings',
				'themezee_blocks_settings',
				array( __CLASS__, 'sanitize_settings' )
			);
		}

		/**
		 * License Section Intro
		 */
		static function license_section_intro() {
			esc_html_e( 'An active license key is needed to install updates and receive support.', 'themezee-icon-heading-block' );
		}

		/**
		 * Sanitize the Plugin Settings
		 *
		 * @param array $input User Input.
		 * @return array
		 */
		static function sanitize_settings( $input = array() ) {

			if ( empty( $_POST['_wp_http_referer'] ) ) {
				return $input;
			}

			$saved = get_option( 'themezee_blocks_settings', array() );
			if ( ! is_array( $saved ) ) {
				$saved = array();
			}

			$input = $input ? $input : array();

			// Loop through each setting being saved and pass it through a sanitization filter.
			foreach ( $input as $key => $value ) :
				$input[ $key ] = sanitize_text_field( $value );
			endforeach;

			return array_merge( $saved, $input );
		}
	}

	// Run Class.
	ThemeZee_Blocks_Admin_Page::setup();

endif;
