<?php
namespace SFPB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend Card Renderer.
 */
class SFPB_Renderer {

	/**
	 * Get and organize post data with appropriate fallbacks.
	 *
	 * @param \WP_Post|int $post Post object or post ID.
	 * @return array Normalized card data.
	 */
	public static function get_card_data( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return array();
		}

		$config = sfpb_get_meta_fields_config();
		$data   = array(
			'id'      => $post->ID,
			'content' => apply_filters( 'the_content', $post->post_content ),
		);

		// Load custom fields
		foreach ( $config as $key => $field_info ) {
			$val = get_post_meta( $post->ID, '_sfpb_' . $key, true );
			if ( '' === $val && isset( $field_info['default'] ) ) {
				$val = $field_info['default'];
			}
			$data[ $key ] = $val;
		}

		// Fallbacks & logic
		// 1. Author Name fallback
		if ( empty( $data['author_name'] ) ) {
			$data['author_name'] = ! empty( $post->post_title ) ? $post->post_title : __( 'Anonymous', 'social-feed-preview-builder' );
		}

		// 2. Author Handle fallback
		if ( empty( $data['author_handle'] ) ) {
			// Generate a handle from the name
			$data['author_handle'] = '@' . sanitize_title( $data['author_name'] );
		} elseif ( 'x' === $data['platform'] && 0 !== strpos( $data['author_handle'], '@' ) ) {
			// Ensure Twitter handle starts with @
			$data['author_handle'] = '@' . $data['author_handle'];
		}

		// 3. Post Date Label fallback
		if ( empty( $data['post_date_label'] ) ) {
			$data['post_date_label'] = get_the_date( '', $post );
		}

		// 4. Media Image URL fallback (Featured Image)
		if ( 'image' === $data['content_type'] && empty( $data['media_url'] ) ) {
			$feat_img = get_the_post_thumbnail_url( $post->ID, 'large' );
			if ( $feat_img ) {
				$data['media_url'] = $feat_img;
			}
		}

		// 5. Initials for missing avatar
		if ( empty( $data['author_avatar_url'] ) ) {
			$data['avatar_fallback'] = self::generate_initials_svg( $data['author_name'] );
		} else {
			$data['avatar_fallback'] = '';
		}

		return $data;
	}

	/**
	 * Generates a clean inline SVG with initials for missing avatars.
	 *
	 * @param string $name Author Name.
	 * @return string Inline SVG string.
	 */
	private static function generate_initials_svg( $name ) {
		$words    = explode( ' ', trim( $name ) );
		$initials = '';
		if ( count( $words ) >= 2 ) {
			$initials = mb_substr( $words[0], 0, 1 ) . mb_substr( $words[1], 0, 1 );
		} elseif ( ! empty( $words[0] ) ) {
			$initials = mb_substr( $words[0], 0, 2 );
		} else {
			$initials = 'U';
		}
		$initials = mb_strtoupper( $initials );

		// Generate a consistent background color based on name hash
		$colors = array(
			'#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5',
			'#2196f3', '#03a9f4', '#00bcd4', '#009688', '#4caf50',
			'#8bc34a', '#ff9800', '#ff5722', '#795548', '#607d8b'
		);
		$color_index = abs( crc32( $name ) ) % count( $colors );
		$bg_color    = $colors[ $color_index ];

		return sprintf(
			'<svg class="sfpb-card__avatar-placeholder" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">' .
			'<circle cx="50" cy="50" r="50" fill="%s" />' .
			'<text x="50%%" y="50%%" dominant-baseline="middle" text-anchor="middle" font-size="36" font-family="Arial, sans-serif" font-weight="bold" fill="#ffffff">%s</text>' .
			'</svg>',
			esc_attr( $bg_color ),
			esc_html( $initials )
		);
	}

	/**
	 * Render the card to HTML.
	 *
	 * @param \WP_Post|int $post Post object or post ID.
	 * @return string Card HTML output.
	 */
	public static function render_card( $post ) {
		$card = self::get_card_data( $post );
		if ( empty( $card ) ) {
			return '';
		}

		ob_start();
		// Extract variables for the template
		$id                = $card['id'];
		$platform          = $card['platform'];
		$content_type      = $card['content_type'];
		$author_name       = $card['author_name'];
		$author_handle     = $card['author_handle'];
		$author_avatar_url = $card['author_avatar_url'];
		$avatar_fallback   = $card['avatar_fallback'];
		$media_url         = $card['media_url'];
		$video_url         = $card['video_url'];
		$external_url      = $card['external_url'];
		$button_label      = $card['button_label'];
		$fake_likes        = $card['fake_likes'];
		$fake_comments     = $card['fake_comments'];
		$fake_shares       = $card['fake_shares'];
		$post_date_label   = $card['post_date_label'];
		$content           = $card['content'];

		// Include card layout template
		include SFPB_PATH . 'templates/feed-card.php';

		return ob_get_clean();
	}
}
