<?php
/**
 * Helper functions and configurations.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all supported platforms.
 *
 * @return array
 */
function sfpb_get_platforms() {
	return array(
		'generic'   => __( 'Generic / Custom', 'social-feed-preview-builder' ),
		'facebook'  => __( 'Facebook (Inspired)', 'social-feed-preview-builder' ),
		'instagram' => __( 'Instagram (Inspired)', 'social-feed-preview-builder' ),
		'x'         => __( 'X / Twitter (Inspired)', 'social-feed-preview-builder' ),
		'linkedin'  => __( 'LinkedIn (Inspired)', 'social-feed-preview-builder' ),
		'tiktok'    => __( 'TikTok (Inspired)', 'social-feed-preview-builder' ),
		'youtube'   => __( 'YouTube (Inspired)', 'social-feed-preview-builder' ),
	);
}

/**
 * Get all supported content types.
 *
 * @return array
 */
function sfpb_get_content_types() {
	return array(
		'text'  => __( 'Text Only', 'social-feed-preview-builder' ),
		'image' => __( 'Image', 'social-feed-preview-builder' ),
		'video' => __( 'Video (Mockup)', 'social-feed-preview-builder' ),
		'link'  => __( 'External Link / CTA', 'social-feed-preview-builder' ),
	);
}

/**
 * Get centralized meta fields configuration.
 * Easy to extend or modify.
 *
 * @return array
 */
function sfpb_get_meta_fields_config() {
	return array(
		'platform' => array(
			'label'       => __( 'Platform', 'social-feed-preview-builder' ),
			'type'        => 'select',
			'options'     => sfpb_get_platforms(),
			'default'     => 'generic',
			'sanitize'    => 'sanitize_key',
		),
		'content_type' => array(
			'label'       => __( 'Content Type', 'social-feed-preview-builder' ),
			'type'        => 'select',
			'options'     => sfpb_get_content_types(),
			'default'     => 'text',
			'sanitize'    => 'sanitize_key',
		),
		'author_name' => array(
			'label'       => __( 'Author Name', 'social-feed-preview-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. John Doe', 'social-feed-preview-builder' ),
		),
		'author_handle' => array(
			'label'       => __( 'Author Handle', 'social-feed-preview-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. @johndoe', 'social-feed-preview-builder' ),
		),
		'author_avatar_url' => array(
			'label'       => __( 'Author Avatar URL', 'social-feed-preview-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => __( 'https://example.com/avatar.jpg', 'social-feed-preview-builder' ),
		),
		'media_url' => array(
			'label'       => __( 'Media Image URL', 'social-feed-preview-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => __( 'https://example.com/image.jpg', 'social-feed-preview-builder' ),
			'dependency'  => 'content_type:image',
		),
		'video_url' => array(
			'label'       => __( 'Video URL (MP4 / Mockup)', 'social-feed-preview-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => __( 'https://example.com/video.mp4', 'social-feed-preview-builder' ),
			'dependency'  => 'content_type:video',
		),
		'external_url' => array(
			'label'       => __( 'External URL / Link CTA', 'social-feed-preview-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => __( 'https://example.com/landing-page', 'social-feed-preview-builder' ),
			'dependency'  => 'content_type:link',
		),
		'button_label' => array(
			'label'       => __( 'Button Label', 'social-feed-preview-builder' ),
			'type'        => 'text',
			'default'     => __( 'Learn More', 'social-feed-preview-builder' ),
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Shop Now', 'social-feed-preview-builder' ),
			'dependency'  => 'content_type:link',
		),
		'campaign_name' => array(
			'label'       => __( 'Campaign Name (for filtering)', 'social-feed-preview-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. summer-launch', 'social-feed-preview-builder' ),
		),
		'fake_likes' => array(
			'label'       => __( 'Likes Count', 'social-feed-preview-builder' ),
			'type'        => 'number',
			'default'     => 0,
			'sanitize'    => 'absint',
			'placeholder' => '0',
		),
		'fake_comments' => array(
			'label'       => __( 'Comments Count', 'social-feed-preview-builder' ),
			'type'        => 'number',
			'default'     => 0,
			'sanitize'    => 'absint',
			'placeholder' => '0',
		),
		'fake_shares' => array(
			'label'       => __( 'Shares/Retweets Count', 'social-feed-preview-builder' ),
			'type'        => 'number',
			'default'     => 0,
			'sanitize'    => 'absint',
			'placeholder' => '0',
		),
		'post_date_label' => array(
			'label'       => __( 'Post Date Label', 'social-feed-preview-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. 2 hours ago, or June 15', 'social-feed-preview-builder' ),
		),
	);
}

/**
 * Sanitize a meta field based on its configuration.
 *
 * @param string $key   Meta field key.
 * @param mixed  $val   Value to sanitize.
 * @return mixed Sanitized value.
 */
function sfpb_sanitize_meta_value( $key, $val ) {
	$config = sfpb_get_meta_fields_config();
	if ( ! isset( $config[ $key ] ) ) {
		return sanitize_text_field( $val );
	}

	$sanitize_func = $config[ $key ]['sanitize'];

	if ( function_exists( $sanitize_func ) ) {
		return call_user_func( $sanitize_func, $val );
	}

	return sanitize_text_field( $val );
}

/**
 * Formats a number to a human-readable string (e.g. 1.2K, 3.4M).
 *
 * @param int $num Number to format.
 * @return string Formatted number.
 */
function sfpb_format_number( $num ) {
	$num = intval( $num );
	if ( $num >= 1000000 ) {
		return round( $num / 1000000, 1 ) . 'M';
	}
	if ( $num >= 1000 ) {
		return round( $num / 1000, 1 ) . 'K';
	}
	return (string) $num;
}

/**
 * Get settings options config.
 *
 * @return array
 */
function sfpb_get_settings_config() {
	return array(
		'default_limit' => array(
			'label'    => __( 'Default Cards Limit', 'social-feed-preview-builder' ),
			'default'  => 6,
			'sanitize' => 'absint',
		),
		'default_columns' => array(
			'label'    => __( 'Default Grid Columns', 'social-feed-preview-builder' ),
			'default'  => 3,
			'sanitize' => 'absint',
		),
		'enable_frontend_js' => array(
			'label'    => __( 'Enable Frontend JavaScript Interactivity', 'social-feed-preview-builder' ),
			'default'  => '1',
			'sanitize' => 'sfpb_sanitize_checkbox',
		),
		'enable_demo_tools' => array(
			'label'    => __( 'Enable Demo Generator and Preview Tools', 'social-feed-preview-builder' ),
			'default'  => '1',
			'sanitize' => 'sfpb_sanitize_checkbox',
		),
		'default_card_radius' => array(
			'label'    => __( 'Default Card Border Radius (px)', 'social-feed-preview-builder' ),
			'default'  => 16,
			'sanitize' => 'absint',
		),
		'default_shadow_style' => array(
			'label'    => __( 'Default Box Shadow style', 'social-feed-preview-builder' ),
			'default'  => 'soft',
			'options'  => array(
				'none'   => __( 'None (Flat)', 'social-feed-preview-builder' ),
				'soft'   => __( 'Soft shadow', 'social-feed-preview-builder' ),
				'medium' => __( 'Medium shadow', 'social-feed-preview-builder' ),
				'strong' => __( 'Strong shadow', 'social-feed-preview-builder' ),
			),
			'sanitize' => 'sanitize_key',
		),
		'enable_full_width' => array(
			'label'    => __( 'Enable Full Width Layout', 'social-feed-preview-builder' ),
			'default'  => '1',
			'sanitize' => 'sfpb_sanitize_checkbox',
		),
		'custom_css' => array(
			'label'    => __( 'Custom CSS Stylesheet Override', 'social-feed-preview-builder' ),
			'default'  => '',
			'sanitize' => 'sfpb_sanitize_custom_css',
		),
	);
}

/**
 * Sanitize custom CSS setting safely to prevent XSS breakout of <style> blocks.
 *
 * @param string $css Input CSS text.
 * @return string Sanitized CSS.
 */
function sfpb_sanitize_custom_css( $css ) {
	$css = wp_strip_all_tags( $css );
	$css = str_ireplace( array( '<style>', '</style>', '<script', '</script>' ), '', $css );
	return $css;
}

/**
 * Sanitize checkbox values to '1' or '0'.
 *
 * @param mixed $value Input value.
 * @return string '1' or '0'.
 */
function sfpb_sanitize_checkbox( $value ) {
	return ( '1' === $value || 1 === $value || true === $value || 'on' === $value ) ? '1' : '0';
}

/**
 * Get custom option value with default fallback.
 *
 * @param string $key Option key suffix.
 * @return mixed Option value.
 */
function sfpb_get_setting( $key ) {
	$config = sfpb_get_settings_config();
	if ( ! isset( $config[ $key ] ) ) {
		return '';
	}

	$option_name = 'sfpb_' . $key;
	$val         = get_option( $option_name, $config[ $key ]['default'] );

	$sanitize_func = $config[ $key ]['sanitize'];
	if ( function_exists( $sanitize_func ) ) {
		return call_user_func( $sanitize_func, $val );
	}

	return $val;
}

/**
 * Map shadow style key to CSS shadow value.
 *
 * @param string $style Style key.
 * @return string CSS box-shadow value.
 */
function sfpb_get_shadow_css( $style ) {
	switch ( $style ) {
		case 'none':
			return 'none';
		case 'medium':
			return '0 15px 35px rgba(0, 0, 0, 0.08), 0 5px 15px rgba(0, 0, 0, 0.04)';
		case 'strong':
			return '0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 20px rgba(0, 0, 0, 0.08)';
		case 'soft':
		default:
			return '0 10px 30px rgba(0, 0, 0, 0.04)';
	}
}

