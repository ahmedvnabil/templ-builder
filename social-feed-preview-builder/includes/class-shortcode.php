<?php
namespace SFPB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode Handler Class.
 */
class SFPB_Shortcode {

	/**
	 * Singleton instance.
	 *
	 * @var SFPB_Shortcode|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return SFPB_Shortcode
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
		add_shortcode( 'social_feed_preview', array( $this, 'handle_shortcode' ) );
	}

	/**
	 * Render the [social_feed_preview] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Output HTML.
	 */
	public function handle_shortcode( $atts ) {
		// Fetch settings defaults for fallback
		$default_limit      = sfpb_get_setting( 'default_limit' );
		$default_columns    = sfpb_get_setting( 'default_columns' );
		$default_full_width = sfpb_get_setting( 'enable_full_width' );

		// Define default attributes
		$atts = shortcode_atts(
			array(
				'id'           => '',
				'platform'     => '',
				'content_type' => '',
				'campaign'     => '',
				'limit'        => $default_limit,
				'columns'      => $default_columns,
				'order'        => 'DESC',
				'orderby'      => 'date',
				'full_width'   => $default_full_width ? 'yes' : 'no',
			),
			$atts,
			'social_feed_preview'
		);

		// Sanitize inputs with Settings fallbacks for empty values
		$limit = ! empty( $atts['limit'] ) ? absint( $atts['limit'] ) : absint( $default_limit );
		if ( $limit <= 0 ) {
			$limit = 6;
		}

		$columns = ! empty( $atts['columns'] ) ? absint( $atts['columns'] ) : absint( $default_columns );
		$columns = min( max( $columns, 1 ), 4 ); // Bound columns between 1 and 4

		$order   = in_array( strtoupper( $atts['order'] ), array( 'ASC', 'DESC' ), true ) ? strtoupper( $atts['order'] ) : 'DESC';
		$orderby = sanitize_key( $atts['orderby'] );
		
		$full_width = filter_var( $atts['full_width'], FILTER_VALIDATE_BOOLEAN ) || in_array( strtolower( $atts['full_width'] ), array( 'yes', 'true', '1' ), true );

		// Setup query arguments
		$query_args = array(
			'post_type'      => 'social_feed_item',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'order'          => $order,
			'orderby'        => $orderby,
		);

		// If a specific post ID is requested, query that post directly
		if ( ! empty( $atts['id'] ) ) {
			$query_args['p'] = absint( $atts['id'] );
		}

		// Build meta queries based on filters
		$meta_query = array();

		// Platform filter (handles comma-separated lists)
		if ( ! empty( $atts['platform'] ) ) {
			$platforms = array_map( 'sanitize_key', array_filter( explode( ',', $atts['platform'] ) ) );
			if ( ! empty( $platforms ) ) {
				$meta_query[] = array(
					'key'     => '_sfpb_platform',
					'value'   => $platforms,
					'compare' => 'IN',
				);
			}
		}

		// Content Type filter (handles comma-separated lists)
		if ( ! empty( $atts['content_type'] ) ) {
			$content_types = array_map( 'sanitize_key', array_filter( explode( ',', $atts['content_type'] ) ) );
			if ( ! empty( $content_types ) ) {
				$meta_query[] = array(
					'key'     => '_sfpb_content_type',
					'value'   => $content_types,
					'compare' => 'IN',
				);
			}
		}

		// Campaign filter
		if ( ! empty( $atts['campaign'] ) ) {
			$meta_query[] = array(
				'key'     => '_sfpb_campaign_name',
				'value'   => sanitize_text_field( $atts['campaign'] ),
				'compare' => '=',
			);
		}

		if ( ! empty( $meta_query ) ) {
			if ( count( $meta_query ) > 1 ) {
				$meta_query['relation'] = 'AND';
			}
			$query_args['meta_query'] = $meta_query;
		}

		// Execute query
		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return '<!-- ' . esc_html__( 'Social Feed Preview: No items found matching attributes.', 'social-feed-preview-builder' ) . ' -->';
		}

		// Enqueue frontend CSS and optional JS dynamically
		wp_enqueue_style( 'sfpb-frontend-css' );

		if ( '1' === sfpb_get_setting( 'enable_frontend_js' ) ) {
			wp_enqueue_script( 'sfpb-frontend-js' );
		}

		// Generate inline CSS variables and custom CSS override injection
		$radius       = absint( sfpb_get_setting( 'default_card_radius' ) );
		$shadow_style = sanitize_key( sfpb_get_setting( 'default_shadow_style' ) );
		$shadow_value = sfpb_get_shadow_css( $shadow_style );
		$custom_css   = sfpb_get_setting( 'custom_css' );

		$inline_css = sprintf(
			':root {
				--sfpb-radius: %dpx;
				--sfpb-shadow: %s;
			}',
			$radius,
			$shadow_value
		);

		if ( ! empty( $custom_css ) ) {
			$inline_css .= "\n" . $custom_css;
		}

		wp_add_inline_style( 'sfpb-frontend-css', $inline_css );

		// Render the grid
		ob_start();

		$cols_class = 'sfpb-feed--cols-' . $columns;
		$container_class = 'sfpb-feed-container';
		if ( $full_width ) {
			$container_class .= ' sfpb-feed-container--full-width';
		}
		?>
		<div class="<?php echo esc_attr( $container_class ); ?>">
			<div class="sfpb-feed <?php echo esc_attr( $cols_class ); ?>">
				<?php
				while ( $query->have_posts() ) {
					$query->the_post();
					echo SFPB_Renderer::render_card( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</div>
		<?php
		wp_reset_postdata();

		return ob_get_clean();
	}
}
