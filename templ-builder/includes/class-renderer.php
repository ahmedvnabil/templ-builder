<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend template renderer and parser class.
 */
class TB_Renderer {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Renderer|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return TB_Renderer
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
	private function __construct() {}

	/**
	 * Normalize a WP_Post object + custom meta fields + taxonomy lists into a clean array structure.
	 *
	 * @param \WP_Post $post Target post object.
	 * @return array Normalized data.
	 */
	public function normalize_post_data( $post ) {
		if ( ! $post instanceof \WP_Post ) {
			$post = get_post( $post );
		}
		if ( ! $post ) {
			return array();
		}

		$fields_config = tb_get_meta_fields_config();
		$normalized    = array(
			'id'      => $post->ID,
			'title'   => get_the_title( $post->ID ),
			'content' => $post->post_content,
			'excerpt' => $post->post_excerpt,
			'image'   => '',
			'image_alt' => '',
		);

		// Extract post thumbnail image
		if ( has_post_thumbnail( $post->ID ) ) {
			$thumb_id   = get_post_thumbnail_id( $post->ID );
			$thumb_url  = wp_get_attachment_image_url( $thumb_id, 'large' );
			$thumb_alt  = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			$normalized['image']     = $thumb_url;
			$normalized['image_alt'] = $thumb_alt ? $thumb_alt : get_the_title( $post->ID );
		}

		// Loop configurations to load meta fields
		foreach ( $fields_config as $key => $config ) {
			if ( 'custom_json_fields' === $key ) {
				continue;
			}
			$val = get_post_meta( $post->ID, '_tb_' . $key, true );
			if ( '' === $val && isset( $config['default'] ) ) {
				$val = $config['default'];
			}
			$normalized[ $key ] = $val;
		}

		// Load custom JSON fields
		$json_raw = get_post_meta( $post->ID, '_tb_custom_json_fields', true );
		$custom   = array();
		if ( ! empty( $json_raw ) ) {
			$decoded = json_decode( $json_raw, true );
			if ( is_array( $decoded ) ) {
				$custom = $decoded;
			}
		}
		$normalized['custom'] = $custom;

		// Extract taxonomy terms
		$types       = array();
		$collections = array();

		$type_terms = get_the_terms( $post->ID, 'templ_type' );
		if ( ! empty( $type_terms ) && ! is_wp_error( $type_terms ) ) {
			$types = wp_list_pluck( $type_terms, 'slug' );
		}

		$collection_terms = get_the_terms( $post->ID, 'templ_collection' );
		if ( ! empty( $collection_terms ) && ! is_wp_error( $collection_terms ) ) {
			$collections = wp_list_pluck( $collection_terms, 'slug' );
		}

		$normalized['types']       = $types;
		$normalized['collections'] = $collections;

		return $normalized;
	}

	/**
	 * Select and include the appropriate layout template file.
	 *
	 * @param string    $template_key Validated template layout key.
	 * @param \WP_Query $query        WP_Query object.
	 * @param int       $columns      Number of grid columns.
	 */
	public function render_template_layout( $template_key, $query, $columns ) {
		// Set templates variables so they are accessible inside layout files.
		$cols_class = 'tb-grid tb-grid--cols-' . $columns;

		switch ( $template_key ) {
			case 'cards-grid':
				include TB_PATH . 'templates/frontend-grid.php';
				break;

			case 'list':
				include TB_PATH . 'templates/frontend-list.php';
				break;

			case 'landing-section':
				include TB_PATH . 'templates/frontend-section.php';
				break;

			case 'feature-section':
				include TB_PATH . 'templates/frontend-feature.php';
				break;

			case 'testimonial':
				include TB_PATH . 'templates/frontend-testimonial.php';
				break;

			case 'faq':
				include TB_PATH . 'templates/frontend-faq.php';
				break;

			case 'portfolio-card':
				include TB_PATH . 'templates/frontend-portfolio.php';
				break;

			case 'single':
				include TB_PATH . 'templates/frontend-single.php';
				break;

			case 'card':
			case 'app-card':
			case 'service-card':
			case 'social-proof':
			case 'minimal':
			default:
				// These are wrapper cards rendered inside a default grid layout
				include TB_PATH . 'templates/frontend-card.php';
				break;
		}
	}

	/**
	 * Safe escaping renderer for custom JSON arrays/fields.
	 *
	 * @param mixed $value Field value inside JSON.
	 * @return string Safe HTML layout.
	 */
	public function render_custom_field_safely( $value ) {
		if ( is_string( $value ) ) {
			return esc_html( $value );
		}
		if ( is_array( $value ) ) {
			// Check if array list
			if ( array_values( $value ) === $value ) {
				$output = '<ul class="tb-custom-list">';
				foreach ( $value as $item ) {
					$output .= '<li>' . esc_html( is_string( $item ) ? $item : wp_json_encode( $item ) ) . '</li>';
				}
				$output .= '</ul>';
				return $output;
			}
			
			// Key-value associative layout
			$output = '<table class="tb-custom-table">';
			foreach ( $value as $k => $v ) {
				$output .= '<tr>';
				$output .= '<th>' . esc_html( $k ) . '</th>';
				$output .= '<td>' . esc_html( is_string( $v ) ? $v : wp_json_encode( $v ) ) . '</td>';
				$output .= '</tr>';
			}
			$output .= '</table>';
			return $output;
		}
		return esc_html( wp_json_encode( $value ) );
	}
}
