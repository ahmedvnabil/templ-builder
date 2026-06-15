<?php
/**
 * Admin Meta Box template.
 *
 * @var array $meta_values Saved meta values.
 * @var \WP_Post $post Current post object.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="tb-meta-box-container">
	<input type="hidden" id="tb_active_meta_tab" name="tb_active_meta_tab" value="display">

	<div class="tb-meta-box-tabs">
		<button type="button" class="tb-meta-tab-btn active" data-tab="display">
			<span class="dashicons dashicons-layout"></span> <?php esc_html_e( 'Template & Display', 'templ-builder' ); ?>
		</button>
		<button type="button" class="tb-meta-tab-btn" data-tab="text">
			<span class="dashicons dashicons-editor-paragraph"></span> <?php esc_html_e( 'Text Content', 'templ-builder' ); ?>
		</button>
		<button type="button" class="tb-meta-tab-btn" data-tab="links">
			<span class="dashicons dashicons-admin-links"></span> <?php esc_html_e( 'Links & CTA', 'templ-builder' ); ?>
		</button>
		<button type="button" class="tb-meta-tab-btn" data-tab="media">
			<span class="dashicons dashicons-format-image"></span> <?php esc_html_e( 'Media', 'templ-builder' ); ?>
		</button>
		<button type="button" class="tb-meta-tab-btn" data-tab="structured">
			<span class="dashicons dashicons-database"></span> <?php esc_html_e( 'Structured Fields', 'templ-builder' ); ?>
		</button>
		<button type="button" class="tb-meta-tab-btn" data-tab="preview">
			<span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Preview Summary', 'templ-builder' ); ?>
		</button>
	</div>

	<!-- TAB 1: Template & Display -->
	<div id="tb-tab-display" class="tb-meta-tab-content active">
		<div class="tb-meta-row">
			<div class="tb-meta-field">
				<label for="tb_template_key"><?php esc_html_e( 'Visual Template Style', 'templ-builder' ); ?></label>
				<select name="tb_template_key" id="tb_template_key" class="tb-input">
					<?php foreach ( tb_get_templates() as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $meta_values['template_key'], $key ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="tb-meta-field">
				<label for="tb_status"><?php esc_html_e( 'Layout Status', 'templ-builder' ); ?></label>
				<select name="tb_status" id="tb_status" class="tb-input">
					<option value="active" <?php selected( $meta_values['status'], 'active' ); ?>><?php esc_html_e( 'Active', 'templ-builder' ); ?></option>
					<option value="draft" <?php selected( $meta_values['status'], 'draft' ); ?>><?php esc_html_e( 'Draft', 'templ-builder' ); ?></option>
					<option value="archived" <?php selected( $meta_values['status'], 'archived' ); ?>><?php esc_html_e( 'Archived', 'templ-builder' ); ?></option>
					<option value="featured" <?php selected( $meta_values['status'], 'featured' ); ?>><?php esc_html_e( 'Featured', 'templ-builder' ); ?></option>
					<option value="coming-soon" <?php selected( $meta_values['status'], 'coming-soon' ); ?>><?php esc_html_e( 'Coming Soon', 'templ-builder' ); ?></option>
				</select>
			</div>
		</div>

		<div class="tb-meta-row tb-meta-row--three">
			<div class="tb-meta-field">
				<label for="tb_priority"><?php esc_html_e( 'Display Priority', 'templ-builder' ); ?></label>
				<input type="number" name="tb_priority" id="tb_priority" class="tb-input" value="<?php echo esc_attr( $meta_values['priority'] ); ?>" placeholder="0">
			</div>

			<div class="tb-meta-field">
				<label for="tb_badge"><?php esc_html_e( 'Overlay Ribbon Badge', 'templ-builder' ); ?></label>
				<input type="text" name="tb_badge" id="tb_badge" class="tb-input" value="<?php echo esc_attr( $meta_values['badge'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Popular, New', 'templ-builder' ); ?>">
			</div>

			<div class="tb-meta-field">
				<label for="tb_icon"><?php esc_html_e( 'Dashicon Identifier', 'templ-builder' ); ?></label>
				<input type="text" name="tb_icon" id="tb_icon" class="tb-input" value="<?php echo esc_attr( $meta_values['icon'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. dashicons-admin-plugins', 'templ-builder' ); ?>">
			</div>
		</div>

		<div class="tb-meta-row">
			<div class="tb-meta-field">
				<label for="tb_accent_color"><?php esc_html_e( 'Accent Hex Color', 'templ-builder' ); ?></label>
				<input type="color" name="tb_accent_color" id="tb_accent_color" class="tb-input" style="height:40px; padding:2px;" value="<?php echo esc_attr( $meta_values['accent_color'] ); ?>">
			</div>

			<div class="tb-meta-field" style="justify-content: center; margin-top: 15px;">
				<label>
					<input type="checkbox" name="tb_featured_item" id="tb_featured_item" value="1" <?php checked( $meta_values['featured_item'], '1' ); ?>>
					<strong><?php esc_html_e( 'Mark as Featured Item', 'templ-builder' ); ?></strong>
				</label>
			</div>
		</div>
	</div>

	<!-- TAB 2: Text Content -->
	<div id="tb-tab-text" class="tb-meta-tab-content">
		<div class="tb-meta-row">
			<div class="tb-meta-field">
				<label for="tb_eyebrow"><?php esc_html_e( 'Eyebrow Text', 'templ-builder' ); ?></label>
				<input type="text" name="tb_eyebrow" id="tb_eyebrow" class="tb-input" value="<?php echo esc_attr( $meta_values['eyebrow'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Limited Offer', 'templ-builder' ); ?>">
			</div>

			<div class="tb-meta-field">
				<label for="tb_subtitle"><?php esc_html_e( 'Subtitle', 'templ-builder' ); ?></label>
				<input type="text" name="tb_subtitle" id="tb_subtitle" class="tb-input" value="<?php echo esc_attr( $meta_values['subtitle'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Simple structured card layouts', 'templ-builder' ); ?>">
			</div>
		</div>

		<div class="tb-meta-row">
			<div class="tb-meta-field tb-meta-field--full">
				<label for="tb_short_description"><?php esc_html_e( 'Short Description / Snippet', 'templ-builder' ); ?></label>
				<textarea name="tb_short_description" id="tb_short_description" class="tb-input" rows="4" placeholder="<?php esc_attr_e( 'A concise text summary of this item...', 'templ-builder' ); ?>"><?php echo esc_textarea( $meta_values['short_description'] ); ?></textarea>
			</div>
		</div>

		<div class="tb-meta-row tb-meta-row--three">
			<div class="tb-meta-field">
				<label for="tb_price"><?php esc_html_e( 'Price Label', 'templ-builder' ); ?></label>
				<input type="text" name="tb_price" id="tb_price" class="tb-input" value="<?php echo esc_attr( $meta_values['price'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. $49/mo, Free', 'templ-builder' ); ?>">
			</div>

			<div class="tb-meta-field">
				<label for="tb_rating"><?php esc_html_e( 'Rating (0 to 5)', 'templ-builder' ); ?></label>
				<input type="text" name="tb_rating" id="tb_rating" class="tb-input" value="<?php echo esc_attr( $meta_values['rating'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. 4.7', 'templ-builder' ); ?>">
			</div>

			<div class="tb-meta-field">
				<label for="tb_role_label"><?php esc_html_e( 'Role / Title Designation', 'templ-builder' ); ?></label>
				<input type="text" name="tb_role_label" id="tb_role_label" class="tb-input" value="<?php echo esc_attr( $meta_values['role_label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. CEO, Architect', 'templ-builder' ); ?>">
			</div>
		</div>

		<div class="tb-meta-row">
			<div class="tb-meta-field">
				<label for="tb_organization"><?php esc_html_e( 'Organization / Company Name', 'templ-builder' ); ?></label>
				<input type="text" name="tb_organization" id="tb_organization" class="tb-input" value="<?php echo esc_attr( $meta_values['organization'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Acme Corp', 'templ-builder' ); ?>">
			</div>

			<div class="tb-meta-field">
				<label for="tb_location_label"><?php esc_html_e( 'Location / Venue', 'templ-builder' ); ?></label>
				<input type="text" name="tb_location_label" id="tb_location_label" class="tb-input" value="<?php echo esc_attr( $meta_values['location_label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. California, US', 'templ-builder' ); ?>">
			</div>
		</div>
	</div>

	<!-- TAB 3: Links & CTA -->
	<div id="tb-tab-links" class="tb-meta-tab-content">
		<div class="tb-meta-row">
			<div class="tb-meta-field">
				<label for="tb_external_url"><?php esc_html_e( 'Primary Button URL / Link', 'templ-builder' ); ?></label>
				<input type="url" name="tb_external_url" id="tb_external_url" class="tb-input" value="<?php echo esc_url( $meta_values['external_url'] ); ?>" placeholder="https://example.com/action">
			</div>

			<div class="tb-meta-field">
				<label for="tb_button_label"><?php esc_html_e( 'Primary Button Label', 'templ-builder' ); ?></label>
				<input type="text" name="tb_button_label" id="tb_button_label" class="tb-input" value="<?php echo esc_attr( $meta_values['button_label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Buy Now', 'templ-builder' ); ?>">
			</div>
		</div>

		<div class="tb-meta-row">
			<div class="tb-meta-field">
				<label for="tb_secondary_url"><?php esc_html_e( 'Secondary Button URL / Link', 'templ-builder' ); ?></label>
				<input type="url" name="tb_secondary_url" id="tb_secondary_url" class="tb-input" value="<?php echo esc_url( $meta_values['secondary_url'] ); ?>" placeholder="https://example.com/secondary">
			</div>

			<div class="tb-meta-field">
				<label for="tb_secondary_button_label"><?php esc_html_e( 'Secondary Button Label', 'templ-builder' ); ?></label>
				<input type="text" name="tb_secondary_button_label" id="tb_secondary_button_label" class="tb-input" value="<?php echo esc_attr( $meta_values['secondary_button_label'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Read Case Study', 'templ-builder' ); ?>">
			</div>
		</div>
	</div>

	<!-- TAB 4: Media -->
	<div id="tb-tab-media" class="tb-meta-tab-content">
		<div class="tb-meta-row">
			<div class="tb-meta-field tb-meta-field--full">
				<label for="tb_media_url"><?php esc_html_e( 'Featured Media File', 'templ-builder' ); ?></label>
				<div class="tb-url-picker">
					<input type="url" name="tb_media_url" id="tb_media_url" class="tb-input tb-url-input" value="<?php echo esc_url( $meta_values['media_url'] ); ?>" placeholder="https://example.com/image.png">
					<button type="button" class="button button-secondary tb-upload-button" data-target="tb_media_url">
						<?php esc_html_e( 'Media Library', 'templ-builder' ); ?>
					</button>
				</div>
				<p class="description"><?php esc_html_e( 'Upload an image/video or select one from your Media library catalog.', 'templ-builder' ); ?></p>
			</div>
		</div>

		<div class="tb-meta-row">
			<div class="tb-meta-field tb-meta-field--full">
				<label for="tb_media_url_alt"><?php esc_html_e( 'Media Alt text', 'templ-builder' ); ?></label>
				<input type="text" name="tb_media_alt" id="tb_media_url_alt" class="tb-input" value="<?php echo esc_attr( $meta_values['media_alt'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Accessible alt layout caption', 'templ-builder' ); ?>">
			</div>
		</div>
	</div>

	<!-- TAB 5: Structured JSON -->
	<div id="tb-tab-structured" class="tb-meta-tab-content">
		<div class="tb-meta-row">
			<div class="tb-meta-field tb-meta-field--full">
				<label for="tb_custom_json_fields"><?php esc_html_e( 'Custom JSON Fields', 'templ-builder' ); ?></label>
				<div class="tb-json-wrapper">
					<textarea name="tb_custom_json_fields" id="tb_custom_json_fields" class="tb-input" rows="8" style="font-family: monospace; font-size: 13px;" placeholder='{
  "tech_stack": ["PHP", "Vanilla JS"],
  "difficulty": "Easy",
  "benefits": ["RTL Layouts", "Zero Dependencies"]
}'><?php echo esc_textarea( $meta_values['custom_json_fields'] ); ?></textarea>
				</div>
				<div class="tb-json-actions">
					<button type="button" class="button button-secondary" id="tb_validate_json_btn"><?php esc_html_e( 'Validate JSON', 'templ-builder' ); ?></button>
					<button type="button" class="button button-secondary" id="tb_format_json_btn"><?php esc_html_e( 'Format JSON', 'templ-builder' ); ?></button>
					<span id="tb_json_validation_badge" class="tb-validation-badge" style="display: none;"></span>
				</div>
				<p class="description">
					<?php esc_html_e( 'Assign structured properties not covered by the default fields. Values will be parsed safely and can be rendered within your template.', 'templ-builder' ); ?>
				</p>
			</div>
		</div>
	</div>

	<!-- TAB 6: Preview Summary -->
	<div id="tb-tab-preview" class="tb-meta-tab-content">
		<div class="tb-admin-card" style="border:none; padding:0; box-shadow:none;">
			<h4 style="margin: 0 0 10px 0; font-size:15px; font-weight:700; border-bottom:1px solid #f0f0f1; padding-bottom:8px;">
				<?php esc_html_e( 'Item Configuration Summary', 'templ-builder' ); ?>
			</h4>
			<ul class="tb-preview-summary-list">
				<li>
					<span class="tb-preview-summary-label"><?php esc_html_e( 'Post Title:', 'templ-builder' ); ?></span>
					<span class="tb-preview-summary-value"><?php echo esc_html( get_the_title( $post->ID ) ); ?></span>
				</li>
				<li>
					<span class="tb-preview-summary-label"><?php esc_html_e( 'Style Template:', 'templ-builder' ); ?></span>
					<span class="tb-preview-summary-value" style="font-weight:600; color:var(--tb-admin-primary);">
						<?php 
						$templates = tb_get_templates();
						echo esc_html( isset( $templates[ $meta_values['template_key'] ] ) ? $templates[ $meta_values['template_key'] ] : $meta_values['template_key'] ); 
						?>
					</span>
				</li>
				<li>
					<span class="tb-preview-summary-label"><?php esc_html_e( 'Layout Status:', 'templ-builder' ); ?></span>
					<span class="tb-preview-summary-value" style="text-transform: capitalize;"><?php echo esc_html( $meta_values['status'] ); ?></span>
				</li>
				<li>
					<span class="tb-preview-summary-label"><?php esc_html_e( 'Primary CTA Link:', 'templ-builder' ); ?></span>
					<span class="tb-preview-summary-value">
						<?php if ( ! empty( $meta_values['external_url'] ) ) : ?>
							<a href="<?php echo esc_url( $meta_values['external_url'] ); ?>" target="_blank"><?php echo esc_html( $meta_values['button_label'] ? $meta_values['button_label'] : __( 'Visit Link', 'templ-builder' ) ); ?> &nearr;</a>
						<?php else : ?>
							<em><?php esc_html_e( 'None', 'templ-builder' ); ?></em>
						<?php endif; ?>
					</span>
				</li>
				<li>
					<span class="tb-preview-summary-label"><?php esc_html_e( 'Visual Shortcode (Single Item):', 'templ-builder' ); ?></span>
					<span class="tb-preview-summary-value">
						<code class="tb-copyable-shortcode" style="background:#f0f0f1; padding:3px 6px; border-radius:4px; font-family:monospace; font-size:12px; cursor:pointer;" title="<?php esc_attr_e( 'Click to copy', 'templ-builder' ); ?>">[templ id="<?php echo esc_attr( $post->ID ); ?>" template="<?php echo esc_attr( $meta_values['template_key'] ); ?>"]</code>
					</span>
				</li>
				<li>
					<span class="tb-preview-summary-label"><?php esc_html_e( 'Structured JSON Payload:', 'templ-builder' ); ?></span>
					<span class="tb-preview-summary-value">
						<?php 
						$raw_json = get_post_meta( $post->ID, '_tb_custom_json_fields', true );
						if ( ! empty( $raw_json ) ) {
							$decoded = json_decode( $raw_json, true );
							if ( is_array( $decoded ) ) {
								printf( '<span style="color:var(--tb-admin-success); font-weight:600;">%s (%d fields)</span>', esc_html__( 'Active & Valid', 'templ-builder' ), count( $decoded ) );
							} else {
								printf( '<span style="color:var(--tb-admin-error); font-weight:600;">%s</span>', esc_html__( 'Malformed / Invalid', 'templ-builder' ) );
							}
						} else {
							echo '<em>' . esc_html__( 'Empty', 'templ-builder' ) . '</em>';
						}
						?>
					</span>
				</li>
			</ul>
		</div>
	</div>
</div>
