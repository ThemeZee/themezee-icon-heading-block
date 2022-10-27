<?php
/**
 * Plugin Name:       ThemeZee Icon Heading Block
 * Description:       Display a SVG icon.
 * Requires at least: 6.0
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            ThemeZee
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       themezee-icon-heading-block
 *
 * @package           ThemeZee Icon Heading Block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function register_themezee_icon_heading_block() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'register_themezee_icon_heading_block' );


/**
 * Set up the Plugin Updater Constants.
 */
define( 'THEMEZEE_ICON_HEADING_BLOCK_VERSION', '1.0' );
define( 'THEMEZEE_ICON_HEADING_BLOCK_NAME', 'Mercia Pro' );
define( 'THEMEZEE_ICON_HEADING_BLOCK_ID', 149520 );
define( 'THEMEZEE_ICON_HEADING_BLOCK_STORE_URL', 'https://themezee.com' );


/**
 * Include License Settings and Plugin Updater.
 */
include dirname( __FILE__ ) . '/includes/class-themezee-blocks-admin-page.php';
include dirname( __FILE__ ) . '/includes/class-themezee-icon-heading-block-license-settings.php';
include dirname( __FILE__ ) . '/includes/class-themezee-icon-heading-block-updater.php';


/**
 * Initialize the updater. Hooked into `init` to work with the
 * wp_version_check cron job, which allows auto-updates.
 */
function update_themezee_icon_heading_block() {

	// To support auto-updates, this needs to run during the wp_version_check cron job for privileged users.
	$doing_cron = defined( 'DOING_CRON' ) && DOING_CRON;
	if ( ! current_user_can( 'manage_options' ) && ! $doing_cron ) {
		return;
	}

	// Retrieve our license key from the DB.
	$options     = get_option( 'themezee_blocks_settings', array() );
	$license_key = ! empty( $options['icon_heading_block_license_key'] ) ? trim( $options['icon_heading_block_license_key'] ) : false;

	// Setup the updater.
	$edd_updater = new ThemeZee_Icon_Heading_Block_Updater(
		THEMEZEE_ICON_HEADING_BLOCK_STORE_URL,
		__FILE__,
		array(
			'version' => THEMEZEE_ICON_HEADING_BLOCK_VERSION,
			'license' => $license_key,
			'item_id' => THEMEZEE_ICON_HEADING_BLOCK_ID,
			'author'  => 'ThemeZee',
			'beta'    => false,
		)
	);
}
add_action( 'init', 'update_themezee_icon_heading_block' );
