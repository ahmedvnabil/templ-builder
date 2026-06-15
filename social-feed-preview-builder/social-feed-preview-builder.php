<?php
/**
 * Plugin Name: Social Feed Preview Builder
 * Plugin URI:  https://github.com/google-gemini/social-feed-preview-builder
 * Description: WordPress plugin to create mock social media feeds and card previews (Facebook, Instagram, X, LinkedIn, TikTok, YouTube, Generic) without using external APIs or React.
 * Version:     0.2.0
 * Author:      Antigravity Agent
 * License:     GPLv2 or later
 * Text Domain: social-feed-preview-builder
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
define( 'SFPB_VERSION', '0.2.0' );
define( 'SFPB_PATH', plugin_dir_path( __FILE__ ) );
define( 'SFPB_URL', plugin_dir_url( __FILE__ ) );

// Include helper functions first.
require_once SFPB_PATH . 'includes/helpers.php';

// Include core plugin class.
require_once SFPB_PATH . 'includes/class-plugin.php';

/**
 * Initialize the plugin.
 */
function sfpb_init_plugin() {
	// Call instance to kick-start.
	\SFPB\includes\SFPB_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'sfpb_init_plugin' );
