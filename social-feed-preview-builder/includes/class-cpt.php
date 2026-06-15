<?php
namespace SFPB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom Post Type Registration and Handling.
 */
class SFPB_CPT {

	/**
	 * Singleton instance.
	 *
	 * @var SFPB_CPT|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return SFPB_CPT
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
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_fields' ) );
		add_filter( 'manage_social_feed_item_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_social_feed_item_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_list_table_assets' ) );
	}

	/**
	 * Enqueue admin assets on the CPT list table page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_list_table_assets( $hook ) {
		global $post_type;
		if ( 'edit.php' === $hook && 'social_feed_item' === $post_type ) {
			wp_enqueue_style( 'sfpb-admin-css' );
			wp_enqueue_script( 'sfpb-admin-js' );
		}
	}

	/**
	 * Register social_feed_item CPT.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Social Feed Items', 'Post type general name', 'social-feed-preview-builder' ),
			'singular_name'      => _x( 'Feed Item', 'Post type singular name', 'social-feed-preview-builder' ),
			'menu_name'          => _x( 'Social Feed Cards', 'Admin Menu text', 'social-feed-preview-builder' ),
			'name_admin_bar'     => _x( 'Social Feed Item', 'Add New on Toolbar', 'social-feed-preview-builder' ),
			'add_new'            => __( 'Add New Feed Item', 'social-feed-preview-builder' ),
			'add_new_item'       => __( 'Add New Feed Item', 'social-feed-preview-builder' ),
			'new_item'           => __( 'New Feed Item', 'social-feed-preview-builder' ),
			'edit_item'          => __( 'Edit Feed Item', 'social-feed-preview-builder' ),
			'view_item'          => __( 'View Feed Item', 'social-feed-preview-builder' ),
			'all_items'          => __( 'All Feed Items', 'social-feed-preview-builder' ),
			'search_items'       => __( 'Search Feed Items', 'social-feed-preview-builder' ),
			'not_found'          => __( 'No feed items found.', 'social-feed-preview-builder' ),
			'not_found_in_trash' => __( 'No feed items found in Trash.', 'social-feed-preview-builder' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'social-feed-item' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-share',
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);

		register_post_type( 'social_feed_item', $args );
	}

	/**
	 * Add Meta Box to the social_feed_item editor.
	 */
	public function add_meta_box() {
		add_meta_box(
			'sfpb_meta_box',
			__( 'Social Feed Preview Settings', 'social-feed-preview-builder' ),
			array( $this, 'render_meta_box' ),
			'social_feed_item',
			'normal',
			'high'
		);
	}

	/**
	 * Render the Meta Box fields using a template file.
	 *
	 * @param \WP_Post $post Current post object.
	 */
	public function render_meta_box( $post ) {
		// Enqueue registered admin style & script.
		wp_enqueue_style( 'sfpb-admin-css' );
		wp_enqueue_script( 'sfpb-admin-js' );
		wp_enqueue_media();

		// Security nonce.
		wp_nonce_field( 'sfpb_save_meta_box', 'sfpb_meta_box_nonce' );

		// Load centralized meta fields and values.
		$fields_config = sfpb_get_meta_fields_config();
		$meta_values   = array();

		foreach ( $fields_config as $key => $config ) {
			$meta_values[ $key ] = get_post_meta( $post->ID, '_sfpb_' . $key, true );
			if ( '' === $meta_values[ $key ] && isset( $config['default'] ) ) {
				$meta_values[ $key ] = $config['default'];
			}
		}

		// Load template.
		include SFPB_PATH . 'templates/admin-meta-box.php';
	}

	/**
	 * Save Meta Box fields on save_post.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_meta_fields( $post_id ) {
		// Verify nonce.
		if ( ! isset( $_POST['sfpb_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['sfpb_meta_box_nonce'], 'sfpb_save_meta_box' ) ) {
			return;
		}

		// Verify autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Verify post type.
		if ( isset( $_POST['post_type'] ) && 'social_feed_item' !== $_POST['post_type'] ) {
			return;
		}

		// Check capability.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save each defined field.
		$fields_config = sfpb_get_meta_fields_config();
		foreach ( $fields_config as $key => $config ) {
			$field_name = 'sfpb_' . $key;
			if ( isset( $_POST[ $field_name ] ) ) {
				$sanitized_val = sfpb_sanitize_meta_value( $key, $_POST[ $field_name ] );
				update_post_meta( $post_id, '_sfpb_' . $key, $sanitized_val );
			} else {
				// If a checkbox/select is not sent or is empty, we handle default or empty depending on design.
				// For text, URLs, numbers, etc. if it's not set in POST but exists in config, we might delete or set to default.
				// Since we are using standard inputs, if it's completely missing (like an unchecked checkbox), we can save empty.
				// But select and text inputs are always sent when the form is submitted, unless disabled.
			}
		}
	}

	/**
	 * Add custom columns to CPT list table.
	 */
	public function add_custom_columns( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $title ) {
			$new_columns[ $key ] = $title;
			if ( 'title' === $key ) {
				$new_columns['sfpb_platform']  = __( 'Platform', 'social-feed-preview-builder' );
				$new_columns['sfpb_shortcode'] = __( 'Shortcode', 'social-feed-preview-builder' );
			}
		}
		return $new_columns;
	}

	/**
	 * Render custom columns contents in CPT list table.
	 */
	public function render_custom_columns( $column, $post_id ) {
		if ( 'sfpb_platform' === $column ) {
			$platform = get_post_meta( $post_id, '_sfpb_platform', true );
			$platforms = sfpb_get_platforms();
			echo esc_html( isset( $platforms[ $platform ] ) ? $platforms[ $platform ] : $platform );
		}

		if ( 'sfpb_shortcode' === $column ) {
			$shortcode = sprintf( '[social_feed_preview id="%d"]', $post_id );
			printf(
				'<code class="sfpb-copyable-shortcode" style="background:#f0f0f1; padding:4px 8px; border-radius:4px; font-family:monospace; font-size:12px; cursor:pointer; display:inline-block;" title="%s">%s</code>',
				esc_attr__( 'Click to copy', 'social-feed-preview-builder' ),
				esc_html( $shortcode )
			);
		}
	}
}
