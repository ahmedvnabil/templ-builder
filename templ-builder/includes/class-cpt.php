<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom Post Type and Taxonomies Handler class.
 */
class TB_CPT {

	/**
	 * Singleton instance.
	 *
	 * @var TB_CPT|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return TB_CPT
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
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		// Custom post list columns
		add_filter( 'manage_templ_item_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_templ_item_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_list_table_assets' ) );
	}

	/**
	 * Enqueue styles/scripts on list table page.
	 *
	 * @param string $hook Admin page hook.
	 */
	public function enqueue_list_table_assets( $hook ) {
		global $post_type;
		if ( 'edit.php' === $hook && 'templ_item' === $post_type ) {
			wp_enqueue_style( 'tb-admin-css' );
			wp_enqueue_script( 'tb-admin-js' );
		}
	}

	/**
	 * Register Custom Post Type templ_item.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Templ Items', 'Post type general name', 'templ-builder' ),
			'singular_name'      => _x( 'Templ Item', 'Post type singular name', 'templ-builder' ),
			'menu_name'          => _x( 'Templ Builder', 'Admin Menu text', 'templ-builder' ),
			'name_admin_bar'     => _x( 'Templ Item', 'Add New on Toolbar', 'templ-builder' ),
			'add_new'            => __( 'Add New Item', 'templ-builder' ),
			'add_new_item'       => __( 'Add New Templ Item', 'templ-builder' ),
			'new_item'           => __( 'New Templ Item', 'templ-builder' ),
			'edit_item'          => __( 'Edit Templ Item', 'templ-builder' ),
			'view_item'          => __( 'View Templ Item', 'templ-builder' ),
			'all_items'          => __( 'All Templ Items', 'templ-builder' ),
			'search_items'       => __( 'Search Templ Items', 'templ-builder' ),
			'not_found'          => __( 'No templ items found.', 'templ-builder' ),
			'not_found_in_trash' => __( 'No templ items found in Trash.', 'templ-builder' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'templ-item' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 26,
			'menu_icon'          => 'dashicons-layout',
			'show_in_rest'       => true, // Gutenberg Editor compatibility
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		);

		register_post_type( 'templ_item', $args );
	}

	/**
	 * Register Taxonomies templ_type and templ_collection.
	 */
	public function register_taxonomies() {
		// 1. Templ Type
		$type_labels = array(
			'name'              => _x( 'Templ Types', 'taxonomy general name', 'templ-builder' ),
			'singular_name'     => _x( 'Templ Type', 'taxonomy singular name', 'templ-builder' ),
			'search_items'      => __( 'Search Templ Types', 'templ-builder' ),
			'all_items'         => __( 'All Templ Types', 'templ-builder' ),
			'parent_item'       => __( 'Parent Templ Type', 'templ-builder' ),
			'parent_item_colon' => __( 'Parent Templ Type:', 'templ-builder' ),
			'edit_item'         => __( 'Edit Templ Type', 'templ-builder' ),
			'update_item'       => __( 'Update Templ Type', 'templ-builder' ),
			'add_new_item'      => __( 'Add New Templ Type', 'templ-builder' ),
			'new_item_name'     => __( 'New Templ Type Name', 'templ-builder' ),
			'menu_name'         => __( 'Types', 'templ-builder' ),
		);

		$type_args = array(
			'hierarchical'      => true,
			'labels'            => $type_labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'templ-type' ),
			'show_in_rest'      => true,
		);

		register_taxonomy( 'templ_type', array( 'templ_item' ), $type_args );

		// 2. Templ Collection
		$collection_labels = array(
			'name'              => _x( 'Templ Collections', 'taxonomy general name', 'templ-builder' ),
			'singular_name'     => _x( 'Templ Collection', 'taxonomy singular name', 'templ-builder' ),
			'search_items'      => __( 'Search Templ Collections', 'templ-builder' ),
			'all_items'         => __( 'All Templ Collections', 'templ-builder' ),
			'parent_item'       => __( 'Parent Templ Collection', 'templ-builder' ),
			'parent_item_colon' => __( 'Parent Templ Collection:', 'templ-builder' ),
			'edit_item'         => __( 'Edit Templ Collection', 'templ-builder' ),
			'update_item'       => __( 'Update Templ Collection', 'templ-builder' ),
			'add_new_item'      => __( 'Add New Templ Collection', 'templ-builder' ),
			'new_item_name'     => __( 'New Templ Collection Name', 'templ-builder' ),
			'menu_name'         => __( 'Collections', 'templ-builder' ),
		);

		$collection_args = array(
			'hierarchical'      => true,
			'labels'            => $collection_labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'templ-collection' ),
			'show_in_rest'      => true,
		);

		register_taxonomy( 'templ_collection', array( 'templ_item' ), $collection_args );
	}

	/**
	 * Custom columns array filters for post list screen.
	 */
	public function add_custom_columns( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $title ) {
			$new_columns[ $key ] = $title;
			if ( 'title' === $key ) {
				$new_columns['tb_type']       = __( 'Type', 'templ-builder' );
				$new_columns['tb_collection'] = __( 'Collection', 'templ-builder' );
				$new_columns['tb_template']   = __( 'Template', 'templ-builder' );
				$new_columns['tb_shortcode']  = __( 'Shortcode', 'templ-builder' );
			}
		}
		return $new_columns;
	}

	/**
	 * Custom columns rendering handlers.
	 */
	public function render_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'tb_type':
				$terms = get_the_terms( $post_id, 'templ_type' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$names = wp_list_pluck( $terms, 'name' );
					echo esc_html( implode( ', ', $names ) );
				} else {
					echo '—';
				}
				break;

			case 'tb_collection':
				$terms = get_the_terms( $post_id, 'templ_collection' );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$names = wp_list_pluck( $terms, 'name' );
					echo esc_html( implode( ', ', $names ) );
				} else {
					echo '—';
				}
				break;

			case 'tb_template':
				$tmpl_key = get_post_meta( $post_id, '_tb_template_key', true );
				$templates = tb_get_templates();
				echo esc_html( isset( $templates[ $tmpl_key ] ) ? $templates[ $tmpl_key ] : ( $tmpl_key ? $tmpl_key : '—' ) );
				break;

			case 'tb_shortcode':
				$shortcode = sprintf( '[templ id="%d"]', $post_id );
				printf(
					'<code class="tb-copyable-shortcode" style="background:#f0f0f1; padding:4px 8px; border-radius:4px; font-family:monospace; font-size:12px; cursor:pointer; display:inline-block;" title="%s">%s</code>',
					esc_attr__( 'Click to copy', 'templ-builder' ),
					esc_html( $shortcode )
				);
				break;
		}
	}
}
