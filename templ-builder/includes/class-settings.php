<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Controller class.
 */
class TB_Settings {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Settings|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return TB_Settings
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
		add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings_fields' ) );
	}

	/**
	 * Register submenu under Settings > Templ Builder.
	 */
	public function register_submenu_page() {
		add_options_page(
			__( 'Templ Builder Settings', 'templ-builder' ),
			__( 'Templ Builder', 'templ-builder' ),
			'manage_options',
			'tb-settings-page',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register Options API settings fields.
	 */
	public function register_settings_fields() {
		$config = tb_get_settings_config();
		foreach ( $config as $key => $field_info ) {
			register_setting(
				'tb_settings_group',
				'tb_' . $key,
				array(
					'type'              => ( 'absint' === $field_info['sanitize'] ) ? 'integer' : 'string',
					'sanitize_callback' => $field_info['sanitize'],
					'default'           => $field_info['default'],
				)
			);
		}
	}

	/**
	 * Render settings page template.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Enqueue assets
		wp_enqueue_style( 'tb-admin-css' );
		wp_enqueue_script( 'tb-admin-js' );

		$config = tb_get_settings_config();
		
		// Load layout view file
		include TB_PATH . 'templates/admin-settings-page.php';
	}
}
