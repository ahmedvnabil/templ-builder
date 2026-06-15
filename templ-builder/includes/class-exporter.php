<?php
namespace TB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JSON backup data exporter and importer class.
 */
class TB_Exporter {

	/**
	 * Singleton instance.
	 *
	 * @var TB_Exporter|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return TB_Exporter
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
	 * Generate and export all templ_item posts as a JSON file.
	 */
	public function export_to_json() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized access.', 'templ-builder' ) );
		}

		$query = new \WP_Query(
			array(
				'post_type'      => 'templ_item',
				'posts_per_page' => -1,
				'post_status'    => 'any',
			)
		);

		$items = array();

		if ( $query->have_posts() ) {
			$fields_config = tb_get_meta_fields_config();
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

				// Base details
				$item = array(
					'title'              => get_the_title(),
					'content'            => get_the_content(),
					'excerpt'            => get_the_excerpt(),
					'menu_order'         => get_post()->menu_order,
					'featured_image_url' => '',
					'types'              => array(),
					'collections'        => array(),
					'meta'               => array(),
				);

				// Featured image URL
				if ( has_post_thumbnail() ) {
					$item['featured_image_url'] = wp_get_attachment_image_url( get_post_thumbnail_id(), 'full' );
				}

				// Taxonomies
				$types = get_the_terms( $post_id, 'templ_type' );
				if ( ! empty( $types ) && ! is_wp_error( $types ) ) {
					$item['types'] = wp_list_pluck( $types, 'name' );
				}

				$collections = get_the_terms( $post_id, 'templ_collection' );
				if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) {
					$item['collections'] = wp_list_pluck( $collections, 'name' );
				}

				// Meta fields config loop
				foreach ( $fields_config as $key => $config ) {
					$val = get_post_meta( $post_id, '_tb_' . $key, true );
					if ( 'custom_json_fields' === $key && ! empty( $val ) ) {
						// Store decoded version inside export array
						$item['meta'][ $key ] = json_decode( $val, true );
					} else {
						$item['meta'][ $key ] = $val;
					}
				}

				$items[] = $item;
			}
			wp_reset_postdata();
		}

		$export_data = array(
			'plugin'      => 'templ-builder',
			'version'     => TB_VERSION,
			'exported_at' => current_time( 'c' ),
			'items'       => $items,
		);

		$json_output = wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		$filename    = 'templ-builder-export-' . current_time( 'Y-m-d-His' ) . '.json';

		// Send download headers
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/json; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . strlen( $json_output ) );

		echo $json_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Import posts from uploaded JSON payload file.
	 *
	 * @param array $file_data Raw uploaded file data $_FILES.
	 * @param bool  $publish   Publish directly instead of draft.
	 * @return int Number of successfully imported items or negative error code.
	 */
	public function import_from_json( $file_data, $publish = false ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return -1; // Unauthorized
		}

		if ( empty( $file_data['tmp_name'] ) || ( ! is_uploaded_file( $file_data['tmp_name'] ) && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) ) {
			return -2; // Upload error
		}

		$content = file_get_contents( $file_data['tmp_name'] );
		$decoded = json_decode( $content, true );

		if ( ! is_array( $decoded ) || ! isset( $decoded['plugin'] ) || 'templ-builder' !== $decoded['plugin'] ) {
			return -3; // Invalid format
		}

		if ( empty( $decoded['items'] ) || ! is_array( $decoded['items'] ) ) {
			return -4; // No items
		}

		// Check capability for immediate publishing
		$post_status = 'draft';
		if ( $publish && current_user_can( 'publish_posts' ) ) {
			$post_status = 'publish';
		}

		$import_count = 0;
		$fields_config = tb_get_meta_fields_config();

		foreach ( $decoded['items'] as $item ) {
			// Basic validation
			if ( empty( $item['title'] ) ) {
				continue;
			}

			// Generate import duplicate detection hash
			$type_slugs       = ! empty( $item['types'] ) ? implode( ',', array_map( 'sanitize_key', $item['types'] ) ) : '';
			$collection_slugs = ! empty( $item['collections'] ) ? implode( ',', array_map( 'sanitize_key', $item['collections'] ) ) : '';
			$hash_source      = $item['title'] . '|' . $type_slugs . '|' . $collection_slugs;
			$import_hash      = md5( $hash_source );

			// Check for duplicates
			$duplicate_query = new \WP_Query(
				array(
					'post_type'      => 'templ_item',
					'posts_per_page' => 1,
					'post_status'    => 'any',
					'meta_query'     => array(
						array(
							'key'   => '_tb_import_hash',
							'value' => $import_hash,
						),
					),
				)
			);
			$is_duplicate = $duplicate_query->have_posts();
			wp_reset_postdata();

			if ( $is_duplicate ) {
				continue; // Skip duplicate
			}

			// Insert post
			$post_id = wp_insert_post(
				array(
					'post_title'   => sanitize_text_field( $item['title'] ),
					'post_content' => wp_kses_post( $item['content'] ),
					'post_excerpt' => sanitize_text_field( $item['excerpt'] ),
					'post_status'  => $post_status,
					'post_type'    => 'templ_item',
					'menu_order'   => isset( $item['menu_order'] ) ? intval( $item['menu_order'] ) : 0,
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				// Store hash
				update_post_meta( $post_id, '_tb_import_hash', $import_hash );

				// Loop taxonomies and assign terms
				if ( ! empty( $item['types'] ) && is_array( $item['types'] ) ) {
					$inserted_terms = array();
					foreach ( $item['types'] as $term_name ) {
						$sanitized_name = sanitize_text_field( $term_name );
						$term = term_exists( $sanitized_name, 'templ_type' );
						if ( ! $term ) {
							$term = wp_insert_term( $sanitized_name, 'templ_type' );
						}
						if ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) {
							$inserted_terms[] = intval( $term['term_id'] );
						}
					}
					wp_set_object_terms( $post_id, $inserted_terms, 'templ_type' );
				}

				if ( ! empty( $item['collections'] ) && is_array( $item['collections'] ) ) {
					$inserted_terms = array();
					foreach ( $item['collections'] as $term_name ) {
						$sanitized_name = sanitize_text_field( $term_name );
						$term = term_exists( $sanitized_name, 'templ_collection' );
						if ( ! $term ) {
							$term = wp_insert_term( $sanitized_name, 'templ_collection' );
						}
						if ( ! is_wp_error( $term ) && isset( $term['term_id'] ) ) {
							$inserted_terms[] = intval( $term['term_id'] );
						}
					}
					wp_set_object_terms( $post_id, $inserted_terms, 'templ_collection' );
				}

				// Loop meta fields
				if ( isset( $item['meta'] ) && is_array( $item['meta'] ) ) {
					foreach ( $fields_config as $key => $config ) {
						if ( isset( $item['meta'][ $key ] ) ) {
							$meta_val = $item['meta'][ $key ];
							if ( 'custom_json_fields' === $key ) {
								if ( is_array( $meta_val ) ) {
									$meta_val = wp_json_encode( $meta_val );
								}
								$sanitized_val = tb_sanitize_custom_json( $meta_val );
							} else {
								$sanitized_val = tb_sanitize_meta_value( $key, $meta_val );
							}
							update_post_meta( $post_id, '_tb_' . $key, $sanitized_val );
						}
					}
				}

				$import_count++;
			}
		}

		return $import_count;
	}
}

/**
 * Type-specific meta value sanitizer wrapper.
 *
 * @param string $key Meta field key.
 * @param mixed  $val Input raw value.
 * @return mixed Sanitized output.
 */
function tb_sanitize_meta_value( $key, $val ) {
	$config = tb_get_meta_fields_config();
	if ( ! isset( $config[ $key ] ) ) {
		return sanitize_text_field( $val );
	}

	$sanitize_func = $config[ $key ]['sanitize'];

	if ( function_exists( $sanitize_func ) ) {
		return call_user_func( $sanitize_func, $val );
	}

	return sanitize_text_field( $val );
}
