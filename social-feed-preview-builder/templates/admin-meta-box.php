<?php
/**
 * Admin Meta Box Template.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sfpb-meta-box-wrapper">
	<!-- Platform & Content Settings -->
	<div class="sfpb-meta-section">
		<h3 class="sfpb-meta-section-title"><?php esc_html_e( '1. Platform & Content Settings', 'social-feed-preview-builder' ); ?></h3>
		<div class="sfpb-meta-row">
			<div class="sfpb-meta-field">
				<label for="sfpb_platform"><?php echo esc_html( $fields_config['platform']['label'] ); ?></label>
				<select name="sfpb_platform" id="sfpb_platform" class="sfpb-input">
					<?php foreach ( $fields_config['platform']['options'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $meta_values['platform'], $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="sfpb-meta-field">
				<label for="sfpb_content_type"><?php echo esc_html( $fields_config['content_type']['label'] ); ?></label>
				<select name="sfpb_content_type" id="sfpb_content_type" class="sfpb-input">
					<?php foreach ( $fields_config['content_type']['options'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $meta_values['content_type'], $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div id="sfpb_platform_note" class="sfpb-help-note"></div>
	</div>

	<!-- Author Settings -->
	<div class="sfpb-meta-section">
		<h3 class="sfpb-meta-section-title"><?php esc_html_e( '2. Author Settings', 'social-feed-preview-builder' ); ?></h3>
		<div class="sfpb-meta-row">
			<div class="sfpb-meta-field">
				<label for="sfpb_author_name"><?php echo esc_html( $fields_config['author_name']['label'] ); ?></label>
				<input type="text" name="sfpb_author_name" id="sfpb_author_name" class="sfpb-input" value="<?php echo esc_attr( $meta_values['author_name'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['author_name']['placeholder'] ); ?>" />
			</div>
			<div class="sfpb-meta-field">
				<label for="sfpb_author_handle"><?php echo esc_html( $fields_config['author_handle']['label'] ); ?></label>
				<input type="text" name="sfpb_author_handle" id="sfpb_author_handle" class="sfpb-input" value="<?php echo esc_attr( $meta_values['author_handle'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['author_handle']['placeholder'] ); ?>" />
			</div>
		</div>
		<div class="sfpb-meta-row">
			<div class="sfpb-meta-field sfpb-meta-field--full">
				<label for="sfpb_author_avatar_url"><?php echo esc_html( $fields_config['author_avatar_url']['label'] ); ?></label>
				<div class="sfpb-url-picker">
					<input type="url" name="sfpb_author_avatar_url" id="sfpb_author_avatar_url" class="sfpb-input sfpb-url-input" value="<?php echo esc_attr( $meta_values['author_avatar_url'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['author_avatar_url']['placeholder'] ); ?>" />
					<button type="button" class="button sfpb-upload-button" data-target="sfpb_author_avatar_url"><?php esc_html_e( 'Select Image', 'social-feed-preview-builder' ); ?></button>
				</div>
			</div>
		</div>
	</div>

	<!-- Media Settings -->
	<div class="sfpb-meta-section">
		<h3 class="sfpb-meta-section-title"><?php esc_html_e( '3. Media Settings', 'social-feed-preview-builder' ); ?></h3>
		<div class="sfpb-meta-row">
			<div class="sfpb-meta-field sfpb-meta-field--full sfpb-conditional" data-dependency="content_type:image">
				<label for="sfpb_media_url"><?php echo esc_html( $fields_config['media_url']['label'] ); ?></label>
				<div class="sfpb-url-picker">
					<input type="url" name="sfpb_media_url" id="sfpb_media_url" class="sfpb-input sfpb-url-input" value="<?php echo esc_attr( $meta_values['media_url'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['media_url']['placeholder'] ); ?>" />
					<button type="button" class="button sfpb-upload-button" data-target="sfpb_media_url"><?php esc_html_e( 'Select Image', 'social-feed-preview-builder' ); ?></button>
				</div>
			</div>
			<div class="sfpb-meta-field sfpb-meta-field--full sfpb-conditional" data-dependency="content_type:video">
				<label for="sfpb_video_url"><?php echo esc_html( $fields_config['video_url']['label'] ); ?></label>
				<div class="sfpb-url-picker">
					<input type="url" name="sfpb_video_url" id="sfpb_video_url" class="sfpb-input sfpb-url-input" value="<?php echo esc_attr( $meta_values['video_url'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['video_url']['placeholder'] ); ?>" />
					<button type="button" class="button sfpb-upload-button" data-target="sfpb_video_url"><?php esc_html_e( 'Select Video', 'social-feed-preview-builder' ); ?></button>
				</div>
				<p class="description"><?php esc_html_e( 'Provide a direct link to an MP4 video file.', 'social-feed-preview-builder' ); ?></p>
			</div>
		</div>
	</div>

	<!-- Engagement Numbers -->
	<div class="sfpb-meta-section">
		<h3 class="sfpb-meta-section-title"><?php esc_html_e( '4. Engagement Numbers', 'social-feed-preview-builder' ); ?></h3>
		<div class="sfpb-meta-row sfpb-meta-row--four">
			<div class="sfpb-meta-field">
				<label for="sfpb_fake_likes"><?php echo esc_html( $fields_config['fake_likes']['label'] ); ?></label>
				<input type="number" min="0" name="sfpb_fake_likes" id="sfpb_fake_likes" class="sfpb-input" value="<?php echo esc_attr( $meta_values['fake_likes'] ); ?>" placeholder="0" />
			</div>
			<div class="sfpb-meta-field">
				<label for="sfpb_fake_comments"><?php echo esc_html( $fields_config['fake_comments']['label'] ); ?></label>
				<input type="number" min="0" name="sfpb_fake_comments" id="sfpb_fake_comments" class="sfpb-input" value="<?php echo esc_attr( $meta_values['fake_comments'] ); ?>" placeholder="0" />
			</div>
			<div class="sfpb-meta-field">
				<label for="sfpb_fake_shares"><?php echo esc_html( $fields_config['fake_shares']['label'] ); ?></label>
				<input type="number" min="0" name="sfpb_fake_shares" id="sfpb_fake_shares" class="sfpb-input" value="<?php echo esc_attr( $meta_values['fake_shares'] ); ?>" placeholder="0" />
			</div>
			<div class="sfpb-meta-field">
				<label for="sfpb_post_date_label"><?php echo esc_html( $fields_config['post_date_label']['label'] ); ?></label>
				<input type="text" name="sfpb_post_date_label" id="sfpb_post_date_label" class="sfpb-input" value="<?php echo esc_attr( $meta_values['post_date_label'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['post_date_label']['placeholder'] ); ?>" />
			</div>
		</div>
	</div>

	<!-- Campaign & CTA -->
	<div class="sfpb-meta-section">
		<h3 class="sfpb-meta-section-title"><?php esc_html_e( '5. Campaign & CTA Settings', 'social-feed-preview-builder' ); ?></h3>
		<div class="sfpb-meta-row">
			<div class="sfpb-meta-field">
				<label for="sfpb_campaign_name"><?php echo esc_html( $fields_config['campaign_name']['label'] ); ?></label>
				<input type="text" name="sfpb_campaign_name" id="sfpb_campaign_name" class="sfpb-input" value="<?php echo esc_attr( $meta_values['campaign_name'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['campaign_name']['placeholder'] ); ?>" />
				<p class="description"><?php esc_html_e( 'Filter feeds using campaign="your-campaign-name" in the shortcode.', 'social-feed-preview-builder' ); ?></p>
			</div>
			<div class="sfpb-conditional sfpb-meta-field" data-dependency="content_type:link">
				<label for="sfpb_button_label"><?php echo esc_html( $fields_config['button_label']['label'] ); ?></label>
				<input type="text" name="sfpb_button_label" id="sfpb_button_label" class="sfpb-input" value="<?php echo esc_attr( $meta_values['button_label'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['button_label']['placeholder'] ); ?>" />
			</div>
		</div>
		<div class="sfpb-meta-row sfpb-conditional" data-dependency="content_type:link">
			<div class="sfpb-meta-field sfpb-meta-field--full">
				<label for="sfpb_external_url"><?php echo esc_html( $fields_config['external_url']['label'] ); ?></label>
				<input type="url" name="sfpb_external_url" id="sfpb_external_url" class="sfpb-input" value="<?php echo esc_attr( $meta_values['external_url'] ); ?>" placeholder="<?php echo esc_attr( $fields_config['external_url']['placeholder'] ); ?>" />
			</div>
		</div>
	</div>
</div>
