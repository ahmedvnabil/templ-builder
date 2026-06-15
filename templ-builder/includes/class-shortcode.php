<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode handler class.
 */
class TB_Shortcode {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Shortcode|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return TB_Shortcode
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
		add_shortcode( 'templ', array( $this, 'handle_shortcode' ) );
	}

	/**
	 * Render the [templ] shortcode on frontend.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Output HTML content.
	 */
	public function handle_shortcode( $atts ) {
		// Fetch fallback default values from Settings page
		$default_template = tb_get_setting( 'default_template' );
		$default_limit    = tb_get_setting( 'default_limit' );
		$max_limit        = tb_get_setting( 'max_limit' );
		$default_columns  = tb_get_setting( 'default_columns' );
		$default_orderby  = tb_get_setting( 'default_orderby' );
		$default_order    = tb_get_setting( 'default_order' );

		// Parse attributes
		$atts = shortcode_atts(
			array(
				'id'           => '',
				'type'         => '',
				'collection'   => '',
				'template'     => $default_template,
				'limit'        => $default_limit,
				'columns'      => $default_columns,
				'orderby'      => $default_orderby,
				'order'        => $default_order,
				'status'       => 'active',
				'featured'     => '',
				'class'        => '',
				'show_empty'   => 'false',
				'full_width'   => 'no', // Dynamic page stretch
			),
			$atts,
			'templ'
		);

		// Sanitize parsed values
		$limit      = absint( $atts['limit'] );
		$limit      = min( $limit, absint( $max_limit ) );
		$columns    = absint( $atts['columns'] );
		$columns    = min( max( $columns, 1 ), 6 );
		$orderby    = sanitize_key( $atts['orderby'] );
		$order      = in_array( strtoupper( $atts['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $atts['order'] ) : 'DESC';
		$status     = sanitize_key( $atts['status'] );
		$class_ext  = sanitize_html_class( $atts['class'] );
		$show_empty = filter_var( $atts['show_empty'], FILTER_VALIDATE_BOOLEAN );
		$full_width = filter_var( $atts['full_width'], FILTER_VALIDATE_BOOLEAN ) || in_array( strtolower( $atts['full_width'] ), array( 'yes', 'true', '1' ), true );

		// Validate template key
		$tmpl_manager = TB_Template_Manager::get_instance();
		$template     = $tmpl_manager->validate_template_key( $atts['template'] );

		// Build WP_Query args
		$query_args = array(
			'post_type'      => 'templ_item',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => $orderby,
			'order'          => $order,
		);

		// Query by Specific ID
		if ( ! empty( $atts['id'] ) ) {
			$query_args['p'] = absint( $atts['id'] );
		}

		// Tax queries
		$tax_query = array();

		if ( ! empty( $atts['type'] ) ) {
			$types = array_map( 'sanitize_key', explode( ',', $atts['type'] ) );
			$tax_query[] = array(
				'taxonomy' => 'templ_type',
				'field'    => 'slug',
				'terms'    => $types,
				'operator' => 'IN',
			);
		}

		if ( ! empty( $atts['collection'] ) ) {
			$collections = array_map( 'sanitize_key', explode( ',', $atts['collection'] ) );
			$tax_query[] = array(
				'taxonomy' => 'templ_collection',
				'field'    => 'slug',
				'terms'    => $collections,
				'operator' => 'IN',
			);
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = $tax_query;
		}

		// Meta queries (Status, Featured item)
		$meta_query = array();

		if ( ! empty( $status ) ) {
			$meta_query[] = array(
				'key'     => '_tb_status',
				'value'   => $status,
				'compare' => '=',
			);
		}

		if ( '1' === $atts['featured'] || 1 === $atts['featured'] || 'true' === $atts['featured'] ) {
			$meta_query[] = array(
				'key'     => '_tb_featured_item',
				'value'   => '1',
				'compare' => '=',
			);
		}

		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = 'AND';
		}
		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		// Run query
		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			if ( $show_empty ) {
				return '<div class="tb-empty-state"><p>' . esc_html__( 'No items found matching the criteria.', 'templ-builder' ) . '</p></div>';
			}
			return '<!-- Templ Builder: No items found -->';
		}

		// Enqueue registered styles and scripts
		wp_enqueue_style( 'tb-frontend-css' );
		if ( '1' === tb_get_setting( 'enable_frontend_js' ) ) {
			wp_enqueue_script( 'tb-frontend-js' );
		}

		// Inject Root styling variables and custom CSS overrides
		$radius       = absint( tb_get_setting( 'default_radius' ) );
		$shadow_style = sanitize_key( tb_get_setting( 'default_shadow' ) );
		$shadow_value = tb_get_shadow_css( $shadow_style );
		$accent_color = sanitize_hex_color( tb_get_setting( 'default_accent_color' ) );
		$custom_css   = tb_get_setting( 'custom_css' );

		$inline_styles = sprintf(
			':root {
				--tb-radius: %dpx;
				--tb-shadow: %s;
				--tb-accent: %s;
				--tb-accent-soft: %s;
			}',
			$radius,
			$shadow_value,
			$accent_color,
			$accent_color . '14' // Add transparency for soft hover states
		);

		if ( ! empty( $custom_css ) ) {
			$inline_styles .= "\n" . $custom_css;
		}

		wp_add_inline_style( 'tb-frontend-css', $inline_styles );

		// Render layouts using layout files
		ob_start();
		$renderer = TB_Renderer::get_instance();
		$wrapper_class = 'tb-wrap';
		if ( $full_width ) {
			$wrapper_class .= ' tb-wrap--full-width';
		}
		if ( ! empty( $class_ext ) ) {
			$wrapper_class .= ' ' . $class_ext;
		}

		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<?php
			$renderer->render_template_layout( $template, $query, $columns );
			?>
		</div>
		<?php
		wp_reset_postdata();

		return ob_get_clean();
	}
}
