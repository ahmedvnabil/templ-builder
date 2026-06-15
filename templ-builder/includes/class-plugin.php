<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin Bootstrap class.
 */
class TB_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Fetch active instance.
	 *
	 * @return TB_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Load core class dependencies.
	 */
	private function load_dependencies() {
		require_once TB_PATH . 'includes/class-cpt.php';
		require_once TB_PATH . 'includes/class-template-manager.php';
		require_once TB_PATH . 'includes/class-renderer.php';
		require_once TB_PATH . 'includes/class-exporter.php';
		require_once TB_PATH . 'includes/class-shortcode.php';
		require_once TB_PATH . 'includes/class-settings.php';
		require_once TB_PATH . 'includes/class-admin.php';
	}

	/**
	 * Initialize hooks and bootstrap classes.
	 */
	private function init_hooks() {
		// Load text domain.
		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Register asset styles and scripts.
		add_action( 'init', array( $this, 'register_assets' ) );

		// Instantiate core classes.
		TB_CPT::get_instance();
		TB_Shortcode::get_instance();
		TB_Settings::get_instance();
		TB_Admin::get_instance();
	}

	/**
	 * Load translation files.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'templ-builder', false, dirname( TB_BASENAME ) . '/languages' );
	}

	/**
	 * Register stylesheet and javascript files.
	 */
	public function register_assets() {
		// Get modification timestamps for cache-busting during development.
		$frontend_css_ver = file_exists( TB_PATH . 'assets/css/frontend.css' ) ? filemtime( TB_PATH . 'assets/css/frontend.css' ) : TB_VERSION;
		$frontend_js_ver  = file_exists( TB_PATH . 'assets/js/frontend.js' )   ? filemtime( TB_PATH . 'assets/js/frontend.js' )   : TB_VERSION;
		$admin_css_ver    = file_exists( TB_PATH . 'assets/css/admin.css' )     ? filemtime( TB_PATH . 'assets/css/admin.css' )     : TB_VERSION;
		$admin_js_ver     = file_exists( TB_PATH . 'assets/js/admin.js' )      ? filemtime( TB_PATH . 'assets/js/admin.js' )      : TB_VERSION;

		// Frontend styles/scripts.
		wp_register_style(
			'tb-frontend-css',
			TB_URL . 'assets/css/frontend.css',
			array(),
			$frontend_css_ver
		);
		wp_register_script(
			'tb-frontend-js',
			TB_URL . 'assets/js/frontend.js',
			array(),
			$frontend_js_ver,
			true
		);

		// Admin styles/scripts.
		wp_register_style(
			'tb-admin-css',
			TB_URL . 'assets/css/admin.css',
			array(),
			$admin_css_ver
		);
		wp_register_script(
			'tb-admin-js',
			TB_URL . 'assets/js/admin.js',
			array(),
			$admin_js_ver,
			true
		);
	}
}
