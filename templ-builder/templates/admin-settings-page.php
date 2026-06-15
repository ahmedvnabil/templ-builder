<?php
/**
 * Admin Settings page template.
 *
 * @var array $config Configuration settings schema.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap tb-tools-wrap">
	<div class="tb-hero">
		<h1><?php esc_html_e( 'Templ Builder Settings', 'templ-builder' ); ?></h1>
		<p><?php esc_html_e( 'Configure fallback rules, grid layouts, and custom theme designs for your templates.', 'templ-builder' ); ?></p>
	</div>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'tb_settings_group' );
		?>

		<div class="tb-admin-card">
			<h2><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'Query & Rendering Defaults', 'templ-builder' ); ?></h2>
			
			<div class="tb-meta-row">
				<div class="tb-meta-field">
					<label for="tb_default_template"><?php esc_html_e( 'Default Layout Template', 'templ-builder' ); ?></label>
					<select name="tb_default_template" id="tb_default_template" class="tb-input">
						<?php foreach ( tb_get_templates() as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( tb_get_setting( 'default_template' ), $key ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Fallback template when none is specified in shortcode attributes.', 'templ-builder' ); ?></p>
				</div>

				<div class="tb-meta-field">
					<label for="tb_default_columns"><?php esc_html_e( 'Default Columns (Desktop)', 'templ-builder' ); ?></label>
					<input type="number" min="1" max="6" name="tb_default_columns" id="tb_default_columns" class="tb-input" value="<?php echo esc_attr( tb_get_setting( 'default_columns' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Default column count for grid layouts.', 'templ-builder' ); ?></p>
				</div>
			</div>

			<div class="tb-meta-row">
				<div class="tb-meta-field">
					<label for="tb_default_limit"><?php esc_html_e( 'Default Items Limit', 'templ-builder' ); ?></label>
					<input type="number" min="1" name="tb_default_limit" id="tb_default_limit" class="tb-input" value="<?php echo esc_attr( tb_get_setting( 'default_limit' ) ); ?>">
				</div>

				<div class="tb-meta-field">
					<label for="tb_max_limit"><?php esc_html_e( 'Maximum Allowable Limit', 'templ-builder' ); ?></label>
					<input type="number" min="1" name="tb_max_limit" id="tb_max_limit" class="tb-input" value="<?php echo esc_attr( tb_get_setting( 'max_limit' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Prevents database performance degradation.', 'templ-builder' ); ?></p>
				</div>
			</div>

			<div class="tb-meta-row">
				<div class="tb-meta-field">
					<label for="tb_default_orderby"><?php esc_html_e( 'Default Order By', 'templ-builder' ); ?></label>
					<select name="tb_default_orderby" id="tb_default_orderby" class="tb-input">
						<option value="date" <?php selected( tb_get_setting( 'default_orderby' ), 'date' ); ?>><?php esc_html_e( 'Published Date', 'templ-builder' ); ?></option>
						<option value="title" <?php selected( tb_get_setting( 'default_orderby' ), 'title' ); ?>><?php esc_html_e( 'Post Title', 'templ-builder' ); ?></option>
						<option value="menu_order" <?php selected( tb_get_setting( 'default_orderby' ), 'menu_order' ); ?>><?php esc_html_e( 'Custom Menu Order (Priority)', 'templ-builder' ); ?></option>
						<option value="rand" <?php selected( tb_get_setting( 'default_orderby' ), 'rand' ); ?>><?php esc_html_e( 'Randomize', 'templ-builder' ); ?></option>
					</select>
				</div>

				<div class="tb-meta-field">
					<label for="tb_default_order"><?php esc_html_e( 'Default Sort Direction', 'templ-builder' ); ?></label>
					<select name="tb_default_order" id="tb_default_order" class="tb-input">
						<option value="DESC" <?php selected( tb_get_setting( 'default_order' ), 'DESC' ); ?>><?php esc_html_e( 'Descending (DESC)', 'templ-builder' ); ?></option>
						<option value="ASC" <?php selected( tb_get_setting( 'default_order' ), 'ASC' ); ?>><?php esc_html_e( 'Ascending (ASC)', 'templ-builder' ); ?></option>
					</select>
				</div>
			</div>
		</div>

		<div class="tb-admin-card">
			<h2><span class="dashicons dashicons-art"></span> <?php esc_html_e( 'Visual Layout Styling', 'templ-builder' ); ?></h2>
			
			<div class="tb-meta-row tb-meta-row--three">
				<div class="tb-meta-field">
					<label for="tb_default_accent_color"><?php esc_html_e( 'Default Accent Color', 'templ-builder' ); ?></label>
					<input type="color" name="tb_default_accent_color" id="tb_default_accent_color" class="tb-input" style="height:40px; padding:2px;" value="<?php echo esc_attr( tb_get_setting( 'default_accent_color' ) ); ?>">
				</div>

				<div class="tb-meta-field">
					<label for="tb_default_radius"><?php esc_html_e( 'Default Corner Border Radius', 'templ-builder' ); ?></label>
					<input type="number" min="0" name="tb_default_radius" id="tb_default_radius" class="tb-input" value="<?php echo esc_attr( tb_get_setting( 'default_radius' ) ); ?>" placeholder="16">
				</div>

				<div class="tb-meta-field">
					<label for="tb_default_shadow"><?php esc_html_e( 'Default Drop Shadow Weight', 'templ-builder' ); ?></label>
					<select name="tb_default_shadow" id="tb_default_shadow" class="tb-input">
						<option value="none" <?php selected( tb_get_setting( 'default_shadow' ), 'none' ); ?>><?php esc_html_e( 'Flat (None)', 'templ-builder' ); ?></option>
						<option value="soft" <?php selected( tb_get_setting( 'default_shadow' ), 'soft' ); ?>><?php esc_html_e( 'Soft (Subtle)', 'templ-builder' ); ?></option>
						<option value="medium" <?php selected( tb_get_setting( 'default_shadow' ), 'medium' ); ?>><?php esc_html_e( 'Medium', 'templ-builder' ); ?></option>
						<option value="strong" <?php selected( tb_get_setting( 'default_shadow' ), 'strong' ); ?>><?php esc_html_e( 'Strong', 'templ-builder' ); ?></option>
					</select>
				</div>
			</div>
		</div>

		<div class="tb-admin-card">
			<h2><span class="dashicons dashicons-plugins-checked"></span> <?php esc_html_e( 'Core Engine Settings', 'templ-builder' ); ?></h2>
			
			<div class="tb-meta-row">
				<div class="tb-meta-field" style="justify-content:center;">
					<label>
						<input type="checkbox" name="tb_enable_frontend_js" value="1" <?php checked( tb_get_setting( 'enable_frontend_js' ), '1' ); ?>>
						<strong><?php esc_html_e( 'Enable Progressive Frontend Javascript', 'templ-builder' ); ?></strong>
					</label>
					<p class="description" style="margin-left: 20px;"><?php esc_html_e( 'Enables accordions and dynamic actions. If disabled, templates fallback gracefully to standard summaries.', 'templ-builder' ); ?></p>
				</div>

				<div class="tb-meta-field" style="justify-content:center;">
					<label>
						<input type="checkbox" name="tb_enable_demo_tools" value="1" <?php checked( tb_get_setting( 'enable_demo_tools' ), '1' ); ?>>
						<strong><?php esc_html_e( 'Enable Demo Content Seeding Dashboard', 'templ-builder' ); ?></strong>
					</label>
				</div>
			</div>

			<div class="tb-meta-row">
				<div class="tb-meta-field" style="justify-content:center;">
					<label>
						<input type="checkbox" name="tb_enable_static_export" value="1" <?php checked( tb_get_setting( 'enable_static_export' ), '1' ); ?>>
						<strong><?php esc_html_e( 'Enable Static HTML Snippet Exports', 'templ-builder' ); ?></strong>
					</label>
				</div>
			</div>
		</div>

		<div class="tb-admin-card">
			<h2><span class="dashicons dashicons-editor-code"></span> <?php esc_html_e( 'Custom CSS Override Stylesheet', 'templ-builder' ); ?></h2>
			<div class="tb-meta-field tb-meta-field--full">
				<textarea name="tb_custom_css" id="tb_custom_css" class="tb-input" rows="8" style="font-family: monospace;" placeholder="/* Custom CSS here. Will be injected with frontend.css inline */"><?php echo esc_textarea( tb_get_setting( 'custom_css' ) ); ?></textarea>
			</div>
		</div>

		<?php submit_button( __( 'Save Configuration Options', 'templ-builder' ), 'primary', 'submit', true, array( 'style' => 'background-color: var(--tb-admin-primary); border-color: var(--tb-admin-primary); text-shadow: none; box-shadow: none;' ) ); ?>
	</form>
</div>
