<?php
/**
 * Helper functions and configurations.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get active template keys.
 *
 * @return array
 */
function tb_get_templates() {
	return array(
		'card'            => __( 'Single Card', 'templ-builder' ),
		'cards-grid'      => __( 'Cards Grid', 'templ-builder' ),
		'list'            => __( 'Compact List', 'templ-builder' ),
		'feature-section' => __( 'Feature Section', 'templ-builder' ),
		'landing-section' => __( 'Landing Page Section (Split)', 'templ-builder' ),
		'testimonial'     => __( 'Testimonial Card', 'templ-builder' ),
		'faq'             => __( 'Accordion FAQ', 'templ-builder' ),
		'portfolio-card'  => __( 'Portfolio Project Card', 'templ-builder' ),
		'app-card'        => __( 'App Directory Card', 'templ-builder' ),
		'service-card'    => __( 'Service/Pricing Card', 'templ-builder' ),
		'social-proof'    => __( 'Social Proof Wall', 'templ-builder' ),
		'minimal'         => __( 'Minimalistic Layout', 'templ-builder' ),
		'single'          => __( 'Single Item Showcase', 'templ-builder' ),
	);
}

/**
 * Get centralized meta fields schema configuration.
 *
 * @return array
 */
function tb_get_meta_fields_config() {
	return array(
		// Template & Display
		'template_key' => array(
			'label'       => __( 'Visual Template Style', 'templ-builder' ),
			'type'        => 'select',
			'options'     => tb_get_templates(),
			'default'     => 'card',
			'sanitize'    => 'sanitize_key',
			'section'     => 'display',
		),
		'priority' => array(
			'label'       => __( 'Display Priority (Ordering)', 'templ-builder' ),
			'type'        => 'number',
			'default'     => 0,
			'sanitize'    => 'intval',
			'placeholder' => '0',
			'section'     => 'display',
		),
		'status' => array(
			'label'       => __( 'Template Layout Status', 'templ-builder' ),
			'type'        => 'select',
			'options'     => array(
				'active'      => __( 'Active', 'templ-builder' ),
				'draft'       => __( 'Draft', 'templ-builder' ),
				'archived'    => __( 'Archived', 'templ-builder' ),
				'featured'    => __( 'Featured', 'templ-builder' ),
				'coming-soon' => __( 'Coming Soon', 'templ-builder' ),
			),
			'default'     => 'active',
			'sanitize'    => 'sanitize_key',
			'section'     => 'display',
		),
		'badge' => array(
			'label'       => __( 'Overlay Ribbon Badge Text', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Popular, New, Sale', 'templ-builder' ),
			'section'     => 'display',
		),
		'icon' => array(
			'label'       => __( 'Icon Identifier (Dashicons class)', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. dashicons-admin-plugins', 'templ-builder' ),
			'section'     => 'display',
		),
		'featured_item' => array(
			'label'       => __( 'Mark as Featured Item', 'templ-builder' ),
			'type'        => 'checkbox',
			'default'     => '0',
			'sanitize'    => 'tb_sanitize_checkbox',
			'section'     => 'display',
		),
		'accent_color' => array(
			'label'       => __( 'Accent Color (Hex value)', 'templ-builder' ),
			'type'        => 'color',
			'default'     => '#2563eb',
			'sanitize'    => 'sanitize_hex_color',
			'section'     => 'display',
		),

		// Text Content
		'eyebrow' => array(
			'label'       => __( 'Eyebrow Text (Small Top Header)', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Special Offer', 'templ-builder' ),
			'section'     => 'text',
		),
		'subtitle' => array(
			'label'       => __( 'Subtitle', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Releasing next month', 'templ-builder' ),
			'section'     => 'text',
		),
		'short_description' => array(
			'label'       => __( 'Short Description / Snippet', 'templ-builder' ),
			'type'        => 'textarea',
			'default'     => '',
			'sanitize'    => 'sanitize_textarea_field',
			'placeholder' => __( 'Brief textual snippet', 'templ-builder' ),
			'section'     => 'text',
		),

		// Links & CTA
		'external_url' => array(
			'label'       => __( 'Primary CTA URL / Action Link', 'templ-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => 'https://example.com/action',
			'section'     => 'links',
		),
		'button_label' => array(
			'label'       => __( 'Primary Button Label', 'templ-builder' ),
			'type'        => 'text',
			'default'     => __( 'Click Here', 'templ-builder' ),
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Learn More, Order Now', 'templ-builder' ),
			'section'     => 'links',
		),
		'secondary_url' => array(
			'label'       => __( 'Secondary Button URL', 'templ-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => 'https://example.com/secondary',
			'section'     => 'links',
		),
		'secondary_button_label' => array(
			'label'       => __( 'Secondary Button Label', 'templ-builder' ),
			'type'        => 'text',
			'default'     => __( 'Read More', 'templ-builder' ),
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. View Case Study', 'templ-builder' ),
			'section'     => 'links',
		),

		// Media
		'media_url' => array(
			'label'       => __( 'Featured Media URL (Image or Video)', 'templ-builder' ),
			'type'        => 'url',
			'default'     => '',
			'sanitize'    => 'esc_url_raw',
			'placeholder' => 'https://example.com/image.jpg',
			'section'     => 'media',
		),
		'media_alt' => array(
			'label'       => __( 'Media Alternative Text (Alt)', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'Screen reader description', 'templ-builder' ),
			'section'     => 'media',
		),

		// Extra Contextual Info
		'rating' => array(
			'label'       => __( 'Rating Value (0 to 5)', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. 4.8 or 5', 'templ-builder' ),
			'section'     => 'extra',
		),
		'price' => array(
			'label'       => __( 'Price Label', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. $49/mo, or Free', 'templ-builder' ),
			'section'     => 'extra',
		),
		'role_label' => array(
			'label'       => __( 'Role / Designation', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Lead Developer', 'templ-builder' ),
			'section'     => 'extra',
		),
		'organization' => array(
			'label'       => __( 'Organization / Company Name', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. Acme Corp', 'templ-builder' ),
			'section'     => 'extra',
		),
		'location_label' => array(
			'label'       => __( 'Location', 'templ-builder' ),
			'type'        => 'text',
			'default'     => '',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => __( 'e.g. New York, USA', 'templ-builder' ),
			'section'     => 'extra',
		),

		// Structured JSON
		'custom_json_fields' => array(
			'label'       => __( 'Custom JSON Structured Fields', 'templ-builder' ),
			'type'        => 'textarea_json',
			'default'     => '',
			'sanitize'    => 'tb_sanitize_custom_json',
			'placeholder' => '{\n  "feature_list": ["Feature A", "Feature B"],\n  "tech_stack": ["PHP", "Vanilla JS"]\n}',
			'section'     => 'structured',
		),
	);
}

/**
 * Get setting value from Options API.
 *
 * @param string $key Setting key.
 * @return mixed Configured value or default.
 */
function tb_get_setting( $key ) {
	$config = tb_get_settings_config();
	if ( ! isset( $config[ $key ] ) ) {
		return null;
	}

	$default = $config[ $key ]['default'];
	return get_option( 'tb_' . $key, $default );
}

/**
 * Settings config helper.
 *
 * @return array
 */
function tb_get_settings_config() {
	return array(
		'default_template' => array(
			'label'    => __( 'Default Layout Template', 'templ-builder' ),
			'default'  => 'card',
			'sanitize' => 'sanitize_key',
		),
		'default_limit' => array(
			'label'    => __( 'Default Cards Query Limit', 'templ-builder' ),
			'default'  => 6,
			'sanitize' => 'absint',
		),
		'max_limit' => array(
			'label'    => __( 'Max Allowable Query Limit', 'templ-builder' ),
			'default'  => 50,
			'sanitize' => 'absint',
		),
		'default_columns' => array(
			'label'    => __( 'Default Columns on Desktop', 'templ-builder' ),
			'default'  => 3,
			'sanitize' => 'absint',
		),
		'default_orderby' => array(
			'label'    => __( 'Default Query Orderby', 'templ-builder' ),
			'default'  => 'date',
			'sanitize' => 'sanitize_key',
		),
		'default_order' => array(
			'label'    => __( 'Default Query Order', 'templ-builder' ),
			'default'  => 'DESC',
			'sanitize' => 'sanitize_key',
		),
		'enable_frontend_js' => array(
			'label'    => __( 'Enable Frontend JavaScript Interactions', 'templ-builder' ),
			'default'  => '1',
			'sanitize' => 'tb_sanitize_checkbox',
		),
		'enable_demo_tools' => array(
			'label'    => __( 'Enable Demo Content Tools Dashboard', 'templ-builder' ),
			'default'  => '1',
			'sanitize' => 'tb_sanitize_checkbox',
		),
		'enable_static_export' => array(
			'label'    => __( 'Enable Static HTML Snippet Exports', 'templ-builder' ),
			'default'  => '1',
			'sanitize' => 'tb_sanitize_checkbox',
		),
		'default_radius' => array(
			'label'    => __( 'Default Border Radius Size (px)', 'templ-builder' ),
			'default'  => 16,
			'sanitize' => 'absint',
		),
		'default_shadow' => array(
			'label'    => __( 'Default Box Shadow Intensity', 'templ-builder' ),
			'default'  => 'soft',
			'options'  => array(
				'none'   => __( 'None (Flat)', 'templ-builder' ),
				'soft'   => __( 'Soft (Subtle)', 'templ-builder' ),
				'medium' => __( 'Medium (Moderate)', 'templ-builder' ),
				'strong' => __( 'Strong (Pronounced)', 'templ-builder' ),
			),
			'sanitize' => 'sanitize_key',
		),
		'default_accent_color' => array(
			'label'    => __( 'Default Accent Theme Color (Hex)', 'templ-builder' ),
			'default'  => '#2563eb',
			'sanitize' => 'sanitize_hex_color',
		),
		'custom_css' => array(
			'label'    => __( 'Custom CSS Stylesheet Override', 'templ-builder' ),
			'default'  => '',
			'sanitize' => 'tb_sanitize_custom_css',
		),
	);
}

/**
 * Checkbox option sanitizer callback.
 *
 * @param mixed $val Input checkbox value.
 * @return string '1' or '0'.
 */
function tb_sanitize_checkbox( $val ) {
	return ( '1' === $val || 1 === $val || true === $val || 'on' === $val ) ? '1' : '0';
}

/**
 * Custom JSON input sanitizer callback.
 *
 * @param string $val Raw JSON text.
 * @return string Sanitized valid JSON or empty.
 */
function tb_sanitize_custom_json( $val ) {
	$trimmed = trim( $val );
	if ( empty( $trimmed ) ) {
		return '';
	}

	$decoded = json_decode( $trimmed, true );
	if ( null === $decoded || false === $decoded ) {
		// Store empty or invalid format flag, we prefer to filter out invalid structures
		return '';
	}

	// Re-serialize back to clean formatted JSON string
	return wp_json_encode( $decoded );
}

/**
 * Custom CSS stylesheet sanitizer callback.
 * Prevents HTML escaping tag breakouts.
 *
 * @param string $css Input stylesheet text.
 * @return string Sanitized CSS code.
 */
function tb_sanitize_custom_css( $css ) {
	return wp_strip_all_tags( $css );
}

/**
 * Renders rating values as stars.
 *
 * @param mixed $rating Rating count/number.
 * @return string HTML rendering of stars.
 */
function tb_render_stars( $rating ) {
	$val = floatval( $rating );
	$val = min( max( $val, 0 ), 5 );

	$full_stars  = floor( $val );
	$half_star   = ( $val - $full_stars >= 0.5 ) ? 1 : 0;
	$empty_stars = 5 - $full_stars - $half_star;

	$output = '';
	for ( $i = 0; $i < $full_stars; $i++ ) {
		$output .= '<span class="tb-star tb-star--full" aria-hidden="true">&#9733;</span>';
	}
	if ( $half_star ) {
		$output .= '<span class="tb-star tb-star--half" aria-hidden="true">&#9733;</span>';
	}
	for ( $i = 0; $i < $empty_stars; $i++ ) {
		$output .= '<span class="tb-star tb-star--empty" aria-hidden="true">&#9734;</span>';
	}

	return '<span class="tb-rating-stars" title="' . esc_attr( sprintf( __( 'Rating: %s out of 5', 'templ-builder' ), $val ) ) . '">' . $output . '</span>';
}

/**
 * Map shadow key to actual box-shadow CSS rule value.
 *
 * @param string $key Shadow type key.
 * @return string Box shadow CSS rule.
 */
function tb_get_shadow_css( $key ) {
	switch ( $key ) {
		case 'none':
			return 'none';
		case 'medium':
			return '0 12px 30px rgba(15, 23, 42, 0.15)';
		case 'strong':
			return '0 20px 45px rgba(15, 23, 42, 0.25)';
		case 'soft':
		default:
			return '0 10px 30px rgba(15, 23, 42, 0.04)';
	}
}
