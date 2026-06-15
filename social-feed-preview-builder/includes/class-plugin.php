<?php
namespace SFPB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin Class.
 */
class SFPB_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var SFPB_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return SFPB_Plugin
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
	 * Load files.
	 */
	private function load_dependencies() {
		require_once SFPB_PATH . 'includes/class-cpt.php';
		require_once SFPB_PATH . 'includes/class-shortcode.php';
		require_once SFPB_PATH . 'includes/class-admin.php';
		require_once SFPB_PATH . 'includes/class-renderer.php';
	}

	/**
	 * Hook initialization.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_assets' ) );

		// Instantiate CPT, Shortcode and Admin classes.
		SFPB_CPT::get_instance();
		SFPB_Shortcode::get_instance();
		SFPB_Admin::get_instance();
	}

	public function register_assets() {
		// Get file modification time for cache busting during active testing
		$frontend_css_ver = file_exists( SFPB_PATH . 'assets/css/frontend.css' ) ? filemtime( SFPB_PATH . 'assets/css/frontend.css' ) : SFPB_VERSION;
		$frontend_js_ver  = file_exists( SFPB_PATH . 'assets/js/frontend.js' )  ? filemtime( SFPB_PATH . 'assets/js/frontend.js' )  : SFPB_VERSION;
		$admin_css_ver    = file_exists( SFPB_PATH . 'assets/css/admin.css' )    ? filemtime( SFPB_PATH . 'assets/css/admin.css' )    : SFPB_VERSION;
		$admin_js_ver     = file_exists( SFPB_PATH . 'assets/js/admin.js' )     ? filemtime( SFPB_PATH . 'assets/js/admin.js' )     : SFPB_VERSION;

		// Frontend CSS & JS
		wp_register_style(
			'sfpb-frontend-css',
			SFPB_URL . 'assets/css/frontend.css',
			array(),
			$frontend_css_ver
		);
		wp_register_script(
			'sfpb-frontend-js',
			SFPB_URL . 'assets/js/frontend.js',
			array(),
			$frontend_js_ver,
			true
		);

		// Admin CSS & JS
		wp_register_style(
			'sfpb-admin-css',
			SFPB_URL . 'assets/css/admin.css',
			array(),
			$admin_css_ver
		);
		wp_register_script(
			'sfpb-admin-js',
			SFPB_URL . 'assets/js/admin.js',
			array(),
			$admin_js_ver,
			true
		);
	}
}
