<?php
/**
 * Plugin Name: Templ Builder
 * Plugin URI:  https://github.com/google-gemini/templ-builder
 * Description: Reusable structured content layouts and template builder using shortcodes and custom fields.
 * Version:     0.1.0
 * Author:      Senior WordPress Engineer
 * License:     GPLv2 or later
 * Text Domain: templ-builder
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 *
 * RTL Support: Yes
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'TB_VERSION', '0.1.0' );
define( 'TB_PATH', plugin_dir_path( __FILE__ ) );
define( 'TB_URL', plugin_dir_url( __FILE__ ) );
define( 'TB_BASENAME', plugin_basename( __FILE__ ) );

// Load helper functions.
require_once TB_PATH . 'includes/helpers.php';

// Load core plugin class.
require_once TB_PATH . 'includes/class-plugin.php';

/**
 * Initialize the plugin bootstrap.
 */
function tb_init_plugin() {
	\TB\includes\TB_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'tb_init_plugin' );
