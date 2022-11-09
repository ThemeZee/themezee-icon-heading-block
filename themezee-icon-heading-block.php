<?php
/**
 * Plugin Name:       ThemeZee Icon Heading Block
 * Description:       Display a SVG icon.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.0
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

	// Load translation for PHP files.
	load_plugin_textdomain( 'themezee-icon-heading-block', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	// Only register blocks if Icon block is activated.
	if ( function_exists( 'register_themezee_icon_block' ) ) {
		register_block_type( __DIR__ . '/build', [
			'title'       => _x( 'Icon Heading', 'block title', 'themezee-icon-heading-block' ),
			'description' => _x( 'Create an icon heading.', 'block description', 'themezee-icon-heading-block' ),
		] );

		// Load translation for JS files.
		wp_set_script_translations( 'themezee-icon-heading-editor-script', 'themezee-icon-heading-block', plugin_dir_path( __FILE__ ) . 'languages' );
	}
}
add_action( 'init', 'register_themezee_icon_heading_block' );


/**
 * Show notice if Icon block is missing.
 */
function themezee_icon_heading_block_admin_notice() {
	global $pagenow;

	if ( ! function_exists( 'register_themezee_icon_block' ) && in_array( $pagenow, array( 'index.php', 'update-core.php', 'plugins.php' ) ) && ! isset( $_GET['page'] ) && current_user_can( 'manage_options' ) ) :
		?>

		<div class="error">
			<p>
				<?php _e( 'The ThemeZee Icon Heading Block needs the ThemeZee Icon Block in order to work. Please install and activate it.', 'themezee-icon-heading-block' ); ?>
			</p>
		</div>

		<?php
	endif;
}
add_action( 'admin_notices', 'themezee_icon_heading_block_admin_notice' );


if ( ! function_exists( 'register_themezee_blocks_block_category' ) ) :
	/**
	 * Add ThemeZee Blocks category to Block Inserter.
	 */
	function register_themezee_blocks_block_category( $categories, $post ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'themezee-blocks',
					'title' => __( 'ThemeZee Blocks', 'themezee-icon-block' ),
				),
			)
		);
	}
	add_filter( 'block_categories_all', 'register_themezee_blocks_block_category', 10, 2 );
endif;


/**
 * Set up the Plugin Updater Constants.
 */
define( 'THEMEZEE_ICON_HEADING_BLOCK_VERSION', '1.0' );
define( 'THEMEZEE_ICON_HEADING_BLOCK_NAME', 'ThemeZee Icon Heading Block' );
define( 'THEMEZEE_ICON_HEADING_BLOCK_ID', 255878 );
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
