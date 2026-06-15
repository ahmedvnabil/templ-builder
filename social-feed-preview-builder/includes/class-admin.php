<?php
namespace SFPB\includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Controller: Menus, Settings, Shortcode Builder, and JSON Import/Export.
 */
class SFPB_Admin {

	/**
	 * Singleton instance.
	 *
	 * @var SFPB_Admin|null
	 */
	private static $instance = null;

	/**
	 * Get active instance.
	 *
	 * @return SFPB_Admin
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
		add_action( 'admin_menu', array( $this, 'register_submenu_pages' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'handle_admin_post_actions' ) );
		add_action( 'init', array( $this, 'auto_seed_on_first_load' ), 20 );
		add_action( 'init', array( $this, 'auto_create_demo_page' ), 25 );
	}

	/**
	 * Register Tools > Social Feed Preview and Settings > Social Feed Preview.
	 */
	public function register_submenu_pages() {
		// Tools Page
		add_submenu_page(
			'tools.php',
			__( 'Social Feed Preview Tools', 'social-feed-preview-builder' ),
			__( 'Social Feed Preview', 'social-feed-preview-builder' ),
			'manage_options',
			'sfpb-tools-page',
			array( $this, 'render_admin_page' )
		);

		// Settings Page
		add_options_page(
			__( 'Social Feed Preview Settings', 'social-feed-preview-builder' ),
			__( 'Social Feed Preview', 'social-feed-preview-builder' ),
			'manage_options',
			'sfpb-settings-page',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings with Options API.
	 */
	public function register_settings() {
		$config = sfpb_get_settings_config();
		foreach ( $config as $key => $field_info ) {
			register_setting(
				'sfpb_settings_group',
				'sfpb_' . $key,
				array(
					'type'              => ( 'absint' === $field_info['sanitize'] ) ? 'integer' : 'string',
					'sanitize_callback' => $field_info['sanitize'],
					'default'           => $field_info['default'],
				)
			);
		}
	}

	/**
	 * Render the Settings Page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_style( 'sfpb-admin-css' );
		wp_enqueue_script( 'sfpb-admin-js' );

		$config = sfpb_get_settings_config();
		?>
		<div class="wrap sfpb-admin-tools-wrap">
			<h1><?php esc_html_e( 'Social Feed Preview Settings', 'social-feed-preview-builder' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'Configure default settings, fallback behaviors, display aesthetics, and custom overrides.', 'social-feed-preview-builder' ); ?>
			</p>

			<form method="post" action="options.php" class="sfpb-admin-card" style="margin-top: 20px;">
				<?php
				settings_fields( 'sfpb_settings_group' );
				?>
				<table class="form-table" role="presentation">
					<tbody>
						<!-- Default Limit -->
						<tr>
							<th scope="row">
								<label for="sfpb_default_limit"><?php echo esc_html( $config['default_limit']['label'] ); ?></label>
							</th>
							<td>
								<input type="number" min="1" max="100" name="sfpb_default_limit" id="sfpb_default_limit" value="<?php echo esc_attr( sfpb_get_setting( 'default_limit' ) ); ?>" class="regular-text" />
								<p class="description"><?php esc_html_e( 'Used when limit attribute is missing in [social_feed_preview] shortcode.', 'social-feed-preview-builder' ); ?></p>
							</td>
						</tr>

						<!-- Default Columns -->
						<tr>
							<th scope="row">
								<label for="sfpb_default_columns"><?php echo esc_html( $config['default_columns']['label'] ); ?></label>
							</th>
							<td>
								<input type="number" min="1" max="4" name="sfpb_default_columns" id="sfpb_default_columns" value="<?php echo esc_attr( sfpb_get_setting( 'default_columns' ) ); ?>" class="regular-text" />
								<p class="description"><?php esc_html_e( 'Default grid columns on desktop (1 to 4). Overridden by columns attribute.', 'social-feed-preview-builder' ); ?></p>
							</td>
						</tr>

						<!-- Enable JS -->
						<tr>
							<th scope="row"><?php esc_html_e( 'Frontend Interactions', 'social-feed-preview-builder' ); ?></th>
							<td>
								<fieldset>
									<label for="sfpb_enable_frontend_js">
										<input type="checkbox" name="sfpb_enable_frontend_js" id="sfpb_enable_frontend_js" value="1" <?php checked( sfpb_get_setting( 'enable_frontend_js' ), '1' ); ?> />
										<?php echo esc_html( $config['enable_frontend_js']['label'] ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Enqueues frontend.js to support mock likes count toggle and video play/pause on click.', 'social-feed-preview-builder' ); ?></p>
								</fieldset>
							</td>
						</tr>

						<!-- Enable Demo Tools -->
						<tr>
							<th scope="row"><?php esc_html_e( 'Demo Tools', 'social-feed-preview-builder' ); ?></th>
							<td>
								<fieldset>
									<label for="sfpb_enable_demo_tools">
										<input type="checkbox" name="sfpb_enable_demo_tools" id="sfpb_enable_demo_tools" value="1" <?php checked( sfpb_get_setting( 'enable_demo_tools' ), '1' ); ?> />
										<?php echo esc_html( $config['enable_demo_tools']['label'] ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Enables the generator seeder and visual preview blocks inside the Tools page.', 'social-feed-preview-builder' ); ?></p>
								</fieldset>
							</td>
						</tr>

						<!-- Border Radius -->
						<tr>
							<th scope="row">
								<label for="sfpb_default_card_radius"><?php echo esc_html( $config['default_card_radius']['label'] ); ?></label>
							</th>
							<td>
								<input type="number" min="0" max="50" name="sfpb_default_card_radius" id="sfpb_default_card_radius" value="<?php echo esc_attr( sfpb_get_setting( 'default_card_radius' ) ); ?>" class="regular-text" />
								<p class="description"><?php esc_html_e( 'Card rounded corner size in pixels (e.g. 16).', 'social-feed-preview-builder' ); ?></p>
							</td>
						</tr>

						<!-- Shadow Style -->
						<tr>
							<th scope="row">
								<label for="sfpb_default_shadow_style"><?php echo esc_html( $config['default_shadow_style']['label'] ); ?></label>
							</th>
							<td>
								<select name="sfpb_default_shadow_style" id="sfpb_default_shadow_style">
									<?php foreach ( $config['default_shadow_style']['options'] as $style_key => $style_label ) : ?>
										<option value="<?php echo esc_attr( $style_key ); ?>" <?php selected( sfpb_get_setting( 'default_shadow_style' ), $style_key ); ?>><?php echo esc_html( $style_label ); ?></option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php esc_html_e( 'Select the visual shadow intensity for cards.', 'social-feed-preview-builder' ); ?></p>
							</td>
						</tr>

						<!-- Full Width Layout -->
						<tr>
							<th scope="row"><?php esc_html_e( 'Full Width Layout', 'social-feed-preview-builder' ); ?></th>
							<td>
								<fieldset>
									<label for="sfpb_enable_full_width">
										<input type="checkbox" name="sfpb_enable_full_width" id="sfpb_enable_full_width" value="1" <?php checked( sfpb_get_setting( 'enable_full_width' ), '1' ); ?> />
										<?php echo esc_html( $config['enable_full_width']['label'] ); ?>
									</label>
									<p class="description"><?php esc_html_e( 'Forces the feed wrapper to span the full browser viewport width (100vw), bypassing boxed theme containers.', 'social-feed-preview-builder' ); ?></p>
								</fieldset>
							</td>
						</tr>

						<!-- Custom CSS Textarea -->
						<tr>
							<th scope="row">
								<label for="sfpb_custom_css"><?php echo esc_html( $config['custom_css']['label'] ); ?></label>
							</th>
							<td>
								<textarea name="sfpb_custom_css" id="sfpb_custom_css" rows="6" cols="50" class="large-text code" placeholder=".sfpb-card { border-color: red; }"><?php echo esc_textarea( sfpb_get_setting( 'custom_css' ) ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Add your custom CSS selectors here. These styles will be loaded on pages displaying the shortcode.', 'social-feed-preview-builder' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the Tools and Shortcode Builder Page.
	 */
	public function render_admin_page() {
		wp_enqueue_style( 'sfpb-admin-css' );
		wp_enqueue_script( 'sfpb-admin-js' );

		wp_enqueue_style( 'sfpb-frontend-css' );
		wp_enqueue_script( 'sfpb-frontend-js' );

		// Check if demo items exist
		$demo_query = new \WP_Query(
			array(
				'post_type'      => 'social_feed_item',
				'posts_per_page' => 10,
				'post_status'    => 'publish',
				'meta_query'     => array(
					array(
						'key'   => '_sfpb_campaign_name',
						'value' => 'sfpb-demo',
					),
				),
			)
		);
		$has_demos        = $demo_query->have_posts();
		$enable_demo_tool = ( '1' === sfpb_get_setting( 'enable_demo_tools' ) );

		// Notices
		$notice_message = '';
		$notice_type    = 'info';
		if ( isset( $_GET['sfpb_action_result'] ) ) {
			$action = sanitize_key( $_GET['sfpb_action_result'] );
			if ( 'success' === $action ) {
				$notice_message = __( 'Demo feed items successfully generated! Scroll down to see the preview.', 'social-feed-preview-builder' );
				$notice_type    = 'success';
			} elseif ( 'deleted' === $action ) {
				$notice_message = __( 'Demo feed items successfully deleted.', 'social-feed-preview-builder' );
				$notice_type    = 'success';
			} elseif ( 'import_success' === $action ) {
				$count          = isset( $_GET['count'] ) ? absint( $_GET['count'] ) : 0;
				$notice_message = sprintf( _n( '%d social feed card successfully imported.', '%d social feed cards successfully imported.', $count, 'social-feed-preview-builder' ), $count );
				$notice_type    = 'success';
			} elseif ( 'import_empty' === $action ) {
				$notice_message = __( 'Import completed. No new items were imported (duplicates skipped).', 'social-feed-preview-builder' );
				$notice_type    = 'info';
			} elseif ( 'import_invalid_file' === $action ) {
				$notice_message = __( 'Import failed. The uploaded file is empty or contains invalid JSON structure.', 'social-feed-preview-builder' );
				$notice_type    = 'error';
			} elseif ( 'import_invalid_data' === $action ) {
				$notice_message = __( 'Import failed. JSON data contains invalid platforms or content types.', 'social-feed-preview-builder' );
				$notice_type    = 'error';
			} elseif ( 'error' === $action ) {
				$notice_message = __( 'Security check failed. You do not have permissions to perform this action.', 'social-feed-preview-builder' );
				$notice_type    = 'error';
			}
		}

		?>
		<div class="wrap sfpb-admin-tools-wrap">
			<h1><?php esc_html_e( 'Social Feed Preview Builder', 'social-feed-preview-builder' ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'Design, customize, and manage visual social media post previews.', 'social-feed-preview-builder' ); ?>
			</p>

			<?php if ( ! empty( $notice_message ) ) : ?>
				<div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible">
					<p><?php echo esc_html( $notice_message ); ?></p>
				</div>
			<?php endif; ?>

			<!-- How to Use -->
			<div class="sfpb-admin-card">
				<h2><?php esc_html_e( 'How to Use', 'social-feed-preview-builder' ); ?></h2>
				<p>
					<?php esc_html_e( '1. Go to', 'social-feed-preview-builder' ); ?>
					<strong><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=social_feed_item' ) ); ?>"><?php esc_html_e( 'Social Feed Cards > Add New Feed Item', 'social-feed-preview-builder' ); ?></a></strong>.
				</p>
				<p>
					<?php esc_html_e( '2. Configure mock settings like Platform style, Likes counts, text content, and publication date.', 'social-feed-preview-builder' ); ?>
				</p>
				<p>
					<?php esc_html_e( '3. Place the shortcode on your website pages.', 'social-feed-preview-builder' ); ?>
				</p>
				<?php
				$demo_page = get_page_by_path( 'social-feed-preview-demo' );
				if ( $demo_page ) :
					?>
					<p>
						<?php esc_html_e( '4. We have automatically created a demo page containing all ready-made shortcodes for you: ', 'social-feed-preview-builder' ); ?>
						<strong><a href="<?php echo esc_url( get_permalink( $demo_page->ID ) ); ?>" target="_blank"><?php esc_html_e( 'View Demo Page', 'social-feed-preview-builder' ); ?></a></strong>.
					</p>
				<?php endif; ?>
			</div>

			<!-- Ready-to-Use Shortcode Examples -->
			<div class="sfpb-admin-card">
				<h2><?php esc_html_e( 'Ready-to-Use Shortcode Examples', 'social-feed-preview-builder' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Click on any shortcode to copy it directly to your clipboard and use it in your editor:', 'social-feed-preview-builder' ); ?>
				</p>
				<table class="wp-list-table widefat fixed striped" style="margin-top: 15px; border-collapse: collapse; width: 100%;">
					<thead>
						<tr>
							<th style="padding: 10px; font-weight: bold; border-bottom: 2px solid #ccd0d4;"><?php esc_html_e( 'Description', 'social-feed-preview-builder' ); ?></th>
							<th style="padding: 10px; font-weight: bold; border-bottom: 2px solid #ccd0d4;"><?php esc_html_e( 'Shortcode (Click to Copy)', 'social-feed-preview-builder' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<strong><?php esc_html_e( 'Default 3-Column Feed', 'social-feed-preview-builder' ); ?></strong>
								<div class="description"><?php esc_html_e( 'Displays the latest 6 items in a 3-column grid.', 'social-feed-preview-builder' ); ?></div>
							</td>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<code class="sfpb-copyable-shortcode" style="background:#f0f0f1; padding:6px 10px; border-radius:4px; font-family:monospace; font-size:13px; cursor:pointer; display:inline-block; border:1px solid #dcdcde; transition: all 0.2s;" title="<?php esc_attr_e( 'Click to copy', 'social-feed-preview-builder' ); ?>">[social_feed_preview limit="6" columns="3"]</code>
							</td>
						</tr>
						<tr>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<strong><?php esc_html_e( 'Instagram Feed Grid', 'social-feed-preview-builder' ); ?></strong>
								<div class="description"><?php esc_html_e( 'Shows the latest 9 Instagram preview cards.', 'social-feed-preview-builder' ); ?></div>
							</td>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<code class="sfpb-copyable-shortcode" style="background:#f0f0f1; padding:6px 10px; border-radius:4px; font-family:monospace; font-size:13px; cursor:pointer; display:inline-block; border:1px solid #dcdcde; transition: all 0.2s;" title="<?php esc_attr_e( 'Click to copy', 'social-feed-preview-builder' ); ?>">[social_feed_preview platform="instagram" limit="9"]</code>
							</td>
						</tr>
						<tr>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<strong><?php esc_html_e( 'Campaign Specific Feed', 'social-feed-preview-builder' ); ?></strong>
								<div class="description"><?php esc_html_e( 'Filters preview cards by campaign name &quot;launch&quot; in a 2-column grid.', 'social-feed-preview-builder' ); ?></div>
							</td>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<code class="sfpb-copyable-shortcode" style="background:#f0f0f1; padding:6px 10px; border-radius:4px; font-family:monospace; font-size:13px; cursor:pointer; display:inline-block; border:1px solid #dcdcde; transition: all 0.2s;" title="<?php esc_attr_e( 'Click to copy', 'social-feed-preview-builder' ); ?>">[social_feed_preview campaign="launch" columns="2"]</code>
							</td>
						</tr>
						<tr>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<strong><?php esc_html_e( 'YouTube Videos Only', 'social-feed-preview-builder' ); ?></strong>
								<div class="description"><?php esc_html_e( 'Displays video mockups filtered for the YouTube platform.', 'social-feed-preview-builder' ); ?></div>
							</td>
							<td style="padding: 12px 10px; vertical-align: middle;">
								<code class="sfpb-copyable-shortcode" style="background:#f0f0f1; padding:6px 10px; border-radius:4px; font-family:monospace; font-size:13px; cursor:pointer; display:inline-block; border:1px solid #dcdcde; transition: all 0.2s;" title="<?php esc_attr_e( 'Click to copy', 'social-feed-preview-builder' ); ?>">[social_feed_preview content_type="video" platform="youtube"]</code>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Live Shortcode Builder -->
			<div class="sfpb-admin-card">
				<h2><?php esc_html_e( 'Live Shortcode Builder', 'social-feed-preview-builder' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Configure attributes below to generate your customized shortcode dynamically:', 'social-feed-preview-builder' ); ?></p>
				
				<div class="sfpb-meta-row" style="margin-top: 15px;">
					<div class="sfpb-meta-field">
						<label for="sfpb_builder_platform"><?php esc_html_e( 'Platform Filter', 'social-feed-preview-builder' ); ?></label>
						<select id="sfpb_builder_platform" class="sfpb-input sfpb-builder-input">
							<option value=""><?php esc_html_e( 'All Platforms', 'social-feed-preview-builder' ); ?></option>
							<?php foreach ( sfpb_get_platforms() as $key => $lbl ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $lbl ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="sfpb-meta-field">
						<label for="sfpb_builder_content_type"><?php esc_html_e( 'Content Type Filter', 'social-feed-preview-builder' ); ?></label>
						<select id="sfpb_builder_content_type" class="sfpb-input sfpb-builder-input">
							<option value=""><?php esc_html_e( 'All Content Types', 'social-feed-preview-builder' ); ?></option>
							<?php foreach ( sfpb_get_content_types() as $key => $lbl ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $lbl ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="sfpb-meta-row">
					<div class="sfpb-meta-field">
						<label for="sfpb_builder_campaign"><?php esc_html_e( 'Campaign (e.g. summer-sale)', 'social-feed-preview-builder' ); ?></label>
						<input type="text" id="sfpb_builder_campaign" class="sfpb-input sfpb-builder-input" placeholder="<?php esc_attr_e( 'Optional', 'social-feed-preview-builder' ); ?>" />
					</div>
					<div class="sfpb-meta-field">
						<div class="sfpb-meta-row" style="margin-bottom:0; gap:10px;">
							<div class="sfpb-meta-field">
								<label for="sfpb_builder_limit"><?php esc_html_e( 'Limit', 'social-feed-preview-builder' ); ?></label>
								<input type="number" id="sfpb_builder_limit" min="1" max="100" class="sfpb-input sfpb-builder-input" value="<?php echo esc_attr( sfpb_get_setting( 'default_limit' ) ); ?>" />
							</div>
							<div class="sfpb-meta-field">
								<label for="sfpb_builder_columns"><?php esc_html_e( 'Columns', 'social-feed-preview-builder' ); ?></label>
								<input type="number" id="sfpb_builder_columns" min="1" max="4" class="sfpb-input sfpb-builder-input" value="<?php echo esc_attr( sfpb_get_setting( 'default_columns' ) ); ?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="sfpb-code-block" style="margin-top: 20px;">
					<span id="sfpb_builder_output">[social_feed_preview]</span>
					<button type="button" class="sfpb-copy-btn" id="sfpb_builder_copy_btn" data-code="[social_feed_preview]"><?php esc_html_e( 'Copy Code', 'social-feed-preview-builder' ); ?></button>
				</div>
			</div>

			<!-- Import & Export Tools -->
			<div class="sfpb-admin-card">
				<h2><?php esc_html_e( 'Backup & Portability (Import / Export)', 'social-feed-preview-builder' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Export all social feed cards as a JSON file or import a saved JSON file to restore items.', 'social-feed-preview-builder' ); ?>
				</p>
				
				<div class="sfpb-meta-row" style="margin-top: 15px;">
					<!-- Export Block -->
					<div style="border-right: 1px solid #eee; padding-right: 20px;">
						<h3><?php esc_html_e( 'Export Cards', 'social-feed-preview-builder' ); ?></h3>
						<p class="description"><?php esc_html_e( 'Download all published cards with their configuration.', 'social-feed-preview-builder' ); ?></p>
						<form method="post" action="" style="margin-top: 15px;">
							<?php wp_nonce_field( 'sfpb_export_action', 'sfpb_export_nonce' ); ?>
							<input type="hidden" name="sfpb_action" value="export_posts" />
							<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Download JSON Export', 'social-feed-preview-builder' ); ?>" />
						</form>
					</div>

					<!-- Import Block -->
					<div style="padding-left: 10px;">
						<h3><?php esc_html_e( 'Import Cards', 'social-feed-preview-builder' ); ?></h3>
						<p class="description"><?php esc_html_e( 'Upload a previously exported JSON file. Duplicates will be automatically skipped.', 'social-feed-preview-builder' ); ?></p>
						<form method="post" action="" enctype="multipart/form-data" style="margin-top: 15px;">
							<?php wp_nonce_field( 'sfpb_import_action', 'sfpb_import_nonce' ); ?>
							<input type="hidden" name="sfpb_action" value="import_posts" />
							<input type="file" name="sfpb_import_file" accept=".json" required style="display:block; margin-bottom: 10px;" />
							<label for="sfpb_publish_on_import" style="display:block; margin-bottom: 15px; font-weight:600;">
								<input type="checkbox" name="sfpb_publish_on_import" id="sfpb_publish_on_import" value="1" />
								<?php esc_html_e( 'Publish imported items immediately (Draft by default)', 'social-feed-preview-builder' ); ?>
							</label>
							<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Upload and Import', 'social-feed-preview-builder' ); ?>" />
						</form>
					</div>
				</div>
			</div>

			<!-- Demo Seeder Block -->
			<?php if ( $enable_demo_tool ) : ?>
				<div class="sfpb-admin-card">
					<h2><?php esc_html_e( 'Demo Content Seeder', 'social-feed-preview-builder' ); ?></h2>
					<p>
						<?php esc_html_e( 'Generate pre-configured cards for Facebook, Instagram, X, LinkedIn, TikTok, YouTube, and Generic style previews instantly.', 'social-feed-preview-builder' ); ?>
					</p>

					<div style="margin-top: 15px;">
						<?php if ( ! $has_demos ) : ?>
							<form method="post" action="">
								<?php wp_nonce_field( 'sfpb_generate_demo_action', 'sfpb_demo_nonce' ); ?>
								<input type="hidden" name="sfpb_action" value="generate_demo" />
								<input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Generate Demo Feed Items', 'social-feed-preview-builder' ); ?>" />
							</form>
						<?php else : ?>
							<div style="display:flex; gap: 10px; align-items: center;">
								<span style="color:#00a32a; font-weight: 600; display: inline-flex; align-items:center; gap:5px;">
									<span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Demo items exist in database.', 'social-feed-preview-builder' ); ?>
								</span>
								<form method="post" action="">
									<?php wp_nonce_field( 'sfpb_generate_demo_action', 'sfpb_demo_nonce' ); ?>
									<input type="hidden" name="sfpb_action" value="delete_demo" />
									<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Delete Demo Items', 'social-feed-preview-builder' ); ?>" onclick="return confirm('<?php esc_js( esc_html_e( 'Are you sure you want to delete all generated demo feed items?', 'social-feed-preview-builder' ) ); ?>');" />
								</form>
							</div>
							<p class="description" style="margin-top:10px;">
								<?php esc_html_e( 'Render the demo items in a page using this shortcode:', 'social-feed-preview-builder' ); ?>
								<code>[social_feed_preview campaign="sfpb-demo" columns="3"]</code>
							</p>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $has_demos ) : ?>
					<div class="sfpb-admin-card">
						<h2><?php esc_html_e( 'Admin Preview Section (All Platforms)', 'social-feed-preview-builder' ); ?></h2>
						<p class="description">
							<?php esc_html_e( 'Real-time look of the generated templates (showing inspired layouts for each platform):', 'social-feed-preview-builder' ); ?>
						</p>
						
						<div class="sfpb-admin-demo-grid sfpb-feed sfpb-feed--cols-3">
							<?php
							while ( $demo_query->have_posts() ) {
								$demo_query->the_post();
								echo SFPB_Renderer::render_card( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							wp_reset_postdata();
							?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Combined POST actions handler (Seeder, JSON Import / Export).
	 */
	public function handle_admin_post_actions() {
		if ( ! isset( $_POST['sfpb_action'] ) ) {
			return;
		}

		$action = sanitize_key( $_POST['sfpb_action'] );

		// 1. Demo Seeding Actions
		if ( 'generate_demo' === $action || 'delete_demo' === $action ) {
			if ( ! isset( $_POST['sfpb_demo_nonce'] ) || ! wp_verify_nonce( $_POST['sfpb_demo_nonce'], 'sfpb_generate_demo_action' ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=error' ) );
				exit;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=error' ) );
				exit;
			}

			if ( 'generate_demo' === $action ) {
				$this->seed_demo_data();
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=success' ) );
				exit;
			} else {
				$this->delete_demo_data();
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=deleted' ) );
				exit;
			}
		}

		// 2. Export Actions
		if ( 'export_posts' === $action ) {
			if ( ! isset( $_POST['sfpb_export_nonce'] ) || ! wp_verify_nonce( $_POST['sfpb_export_nonce'], 'sfpb_export_action' ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=error' ) );
				exit;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=error' ) );
				exit;
			}
			$this->export_posts();
		}

		// 3. Import Actions
		if ( 'import_posts' === $action ) {
			if ( ! isset( $_POST['sfpb_import_nonce'] ) || ! wp_verify_nonce( $_POST['sfpb_import_nonce'], 'sfpb_import_action' ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=error' ) );
				exit;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=error' ) );
				exit;
			}
			$this->import_posts();
		}
	}

	/**
	 * Export published social feed cards to JSON.
	 */
	private function export_posts() {
		$query = new \WP_Query(
			array(
				'post_type'      => 'social_feed_item',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			)
		);

		$export_data = array();
		$config      = sfpb_get_meta_fields_config();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				$meta = array();
				foreach ( $config as $key => $field_info ) {
					$meta[ $key ] = get_post_meta( $post->ID, '_sfpb_' . $key, true );
				}

				$export_data[] = array(
					'title'   => $post->post_title,
					'content' => $post->post_content,
					'meta'    => $meta,
				);
			}
		}
		wp_reset_postdata();

		$json_output = wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=social-feed-cards-export-' . date( 'Y-m-d' ) . '.json' );
		echo $json_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Import social feed cards from a JSON file.
	 */
	private function import_posts() {
		if ( empty( $_FILES['sfpb_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_invalid_file' ) );
			exit;
		}

		$file_path = sanitize_text_field( $_FILES['sfpb_import_file']['tmp_name'] );
		$content   = file_get_contents( $file_path );
		$data      = json_decode( $content, true );

		if ( ! is_array( $data ) ) {
			wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_invalid_file' ) );
			exit;
		}

		$platforms           = sfpb_get_platforms();
		$content_types       = sfpb_get_content_types();
		$import_count        = 0;
		$publish_immediately = isset( $_POST['sfpb_publish_on_import'] ) && '1' === $_POST['sfpb_publish_on_import'];
		$post_status         = 'draft';

		if ( $publish_immediately && current_user_can( 'publish_posts' ) ) {
			$post_status = 'publish';
		}

		foreach ( $data as $item ) {
			// Basic Validation
			if ( ! isset( $item['title'] ) || ! isset( $item['content'] ) || ! isset( $item['meta'] ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_invalid_file' ) );
				exit;
			}

			$meta = $item['meta'];
			
			// Validate Platform & Content Type
			if ( ! isset( $meta['platform'] ) || ! isset( $platforms[ $meta['platform'] ] ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_invalid_data' ) );
				exit;
			}
			if ( ! isset( $meta['content_type'] ) || ! isset( $content_types[ $meta['content_type'] ] ) ) {
				wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_invalid_data' ) );
				exit;
			}

			// Duplicate Check
			$duplicate = new \WP_Query(
				array(
					'post_type'      => 'social_feed_item',
					'title'          => $item['title'],
					'posts_per_page' => 1,
					'meta_query'     => array(
						array(
							'key'     => '_sfpb_platform',
							'value'   => $meta['platform'],
							'compare' => '=',
						),
					),
				)
			);

			if ( $duplicate->have_posts() ) {
				wp_reset_postdata();
				continue; // Skip duplicates
			}
			wp_reset_postdata();

			// Insert Post
			$post_id = wp_insert_post(
				array(
					'post_title'   => sanitize_text_field( $item['title'] ),
					'post_content' => wp_kses_post( $item['content'] ),
					'post_status'  => $post_status,
					'post_type'    => 'social_feed_item',
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$import_count++;
				// Save sanitized meta
				$config = sfpb_get_meta_fields_config();
				foreach ( $config as $key => $field_info ) {
					$meta_val = isset( $meta[ $key ] ) ? $meta[ $key ] : $field_info['default'];
					$sanitized = sfpb_sanitize_meta_value( $key, $meta_val );
					update_post_meta( $post_id, '_sfpb_' . $key, $sanitized );
				}
			}
		}

		if ( $import_count > 0 ) {
			wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_success&count=' . $import_count ) );
		} else {
			wp_safe_redirect( admin_url( 'tools.php?page=sfpb-tools-page&sfpb_action_result=import_empty' ) );
		}
		exit;
	}

	/**
	 * Seeds 7 representative mockup posts into the database.
	 */
	public function seed_demo_data() {
		$demo_items = array(
			array(
				'title'   => 'تحديث منصة فيسبوك التجريبية',
				'content' => 'مرحباً بكم في منصتنا الجديدة! نحن سعداء للغاية بالإعلان عن إطلاق ميزاتنا الجديدة التي ستحدث فرقاً في أداء أعمالكم. #تحديث #تكنولوجيا #ريادة_الأعمال',
				'meta'    => array(
					'platform'          => 'facebook',
					'content_type'      => 'image',
					'author_name'       => 'أحمد القحطاني',
					'author_handle'     => 'ahmed_qahtani',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&h=150&q=80',
					'media_url'         => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&h=450&q=80',
					'fake_likes'        => 342,
					'fake_comments'     => 48,
					'fake_shares'       => 19,
					'post_date_label'   => 'أمس الساعة ٣:١٢ م',
					'campaign_name'     => 'sfpb-demo',
				),
			),
			array(
				'title'   => 'Instagram Square Promo',
				'content' => 'صورة تساوي ألف كلمة! ترقبوا تفاصيل حصرية عن أحدث منتجاتنا قريباً. 🚀✨ #تصميم #إبداع #جديد',
				'meta'    => array(
					'platform'          => 'instagram',
					'content_type'      => 'image',
					'author_name'       => 'سارة عبد الله',
					'author_handle'     => 'sara_design',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&h=150&q=80',
					'media_url'         => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=800&h=800&q=80',
					'fake_likes'        => 1205,
					'fake_comments'     => 94,
					'post_date_label'   => '2 hours ago',
					'campaign_name'     => 'sfpb-demo',
				),
			),
			array(
				'title'   => 'X Post testimonial',
				'content' => 'التفاصيل الصغيرة هي التي تصنع الفرق. نعمل على تحسين كل شبر من التجربة لتقديم أفضل أداء ممكن لعملائنا في الشرق الأوسط. ما هي توقعاتكم للتحديث الجديد؟ 👇 #X #تحديث',
				'meta'    => array(
					'platform'          => 'x',
					'content_type'      => 'text',
					'author_name'       => 'خالد الحربي',
					'author_handle'     => 'khalid_tech',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&h=150&q=80',
					'fake_likes'        => 892,
					'fake_comments'     => 143,
					'fake_shares'       => 56,
					'post_date_label'   => 'June 15',
					'campaign_name'     => 'sfpb-demo',
				),
			),
			array(
				'title'   => 'LinkedIn Professional Success Story',
				'content' => 'فخور بمشاركة قصة نجاح فريقنا في الربع الأول من هذا العام. كيف تمكنا من تحقيق نمو بنسبة 40% من خلال التركيز على رضا العميل وتطوير البنية التحتية للمنتج. اقرأ التفاصيل كاملة في المقال التالي.',
				'meta'    => array(
					'platform'          => 'linkedin',
					'content_type'      => 'link',
					'author_name'       => 'د. يوسف العمري',
					'author_handle'     => 'CEO @ AcmeCorp',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=150&h=150&q=80',
					'media_url'         => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&h=450&q=80',
					'external_url'      => 'https://example.com/growth-article',
					'button_label'      => 'Read Article',
					'fake_likes'        => 435,
					'fake_comments'     => 32,
					'fake_shares'       => 12,
					'post_date_label'   => '1 week ago',
					'campaign_name'     => 'sfpb-demo',
				),
			),
			array(
				'title'   => 'TikTok Video Mockup',
				'content' => 'تابعونا في هذه الجولة السريعة خلف الكواليس لرؤية كيف نجهز طلباتكم اليومية بكل حب! 📦🔥 #كواليس #عمل #تعبئة #توصيل',
				'meta'    => array(
					'platform'          => 'tiktok',
					'content_type'      => 'video',
					'author_name'       => 'متجر أوريجينال',
					'author_handle'     => 'original_store',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&h=150&q=80',
					'video_url'         => 'https://assets.mixkit.co/videos/preview/mixkit-taking-photos-of-a-fashion-model-41551-large.mp4',
					'fake_likes'        => 24500,
					'fake_comments'     => 380,
					'fake_shares'       => 560,
					'post_date_label'   => '3d ago',
					'campaign_name'     => 'sfpb-demo',
				),
			),
			array(
				'title'   => 'YouTube Masterclass Guide',
				'content' => 'شرح تفصيلي كامل لكيفية بناء تطبيقات سريعة وlightweight بدون تعقيدات الأدوات الحديثة. لا تنسوا الاشتراك في القناة وتفعيل الجرس ليصلكم كل جديد!',
				'meta'    => array(
					'platform'          => 'youtube',
					'content_type'      => 'video',
					'author_name'       => 'تعلم البرمجة ببساطة',
					'author_handle'     => 'easy_code_arabic',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&h=150&q=80',
					'video_url'         => 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-programmer-typing-on-a-keyboard-40618-large.mp4',
					'fake_likes'        => 5600,
					'fake_comments'     => 410,
					'post_date_label'   => '2 weeks ago',
					'campaign_name'     => 'sfpb-demo',
				),
			),
			array(
				'title'   => 'Generic Custom Banner CTA',
				'content' => 'احصل على نسختك التجريبية المجانية اليوم وابدأ في تخصيص واجهاتك الاجتماعية بنفسك! لا حاجة لخبرة برمجية.',
				'meta'    => array(
					'platform'          => 'generic',
					'content_type'      => 'link',
					'author_name'       => 'المنتج الذكي',
					'author_handle'     => 'smart_product',
					'author_avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&h=150&q=80',
					'media_url'         => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=800&h=450&q=80',
					'external_url'      => 'https://example.com/trial',
					'button_label'      => 'ابدأ الآن',
					'fake_likes'        => 150,
					'fake_comments'     => 8,
					'fake_shares'       => 4,
					'post_date_label'   => '10 mins ago',
					'campaign_name'     => 'sfpb-demo',
				),
			),
		);

		foreach ( $demo_items as $item ) {
			$post_id = wp_insert_post(
				array(
					'post_title'   => $item['title'],
					'post_content' => $item['content'],
					'post_status'  => 'publish',
					'post_type'    => 'social_feed_item',
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				foreach ( $item['meta'] as $meta_key => $meta_val ) {
					update_post_meta( $post_id, '_sfpb_' . $meta_key, $meta_val );
				}
			}
		}
	}

	/**
	 * Deletes all generated demo items from database.
	 */
	private function delete_demo_data() {
		$query = new \WP_Query(
			array(
				'post_type'      => 'social_feed_item',
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'   => '_sfpb_campaign_name',
						'value' => 'sfpb-demo',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				wp_delete_post( get_the_ID(), true );
			}
		}
		wp_reset_postdata();
	}

	/**
	 * Automatically seeds demo data on first plugin load if empty.
	 */
	public function auto_seed_on_first_load() {
		if ( ! get_option( 'sfpb_demo_seeded' ) ) {
			$query = new \WP_Query(
				array(
					'post_type'      => 'social_feed_item',
					'posts_per_page' => 1,
					'post_status'    => 'any',
				)
			);
			if ( ! $query->have_posts() ) {
				$this->seed_demo_data();
			}
			wp_reset_postdata();

			update_option( 'sfpb_demo_seeded', '1' );
		}
	}

	/**
	 * Automatically creates a demo page containing all ready-made shortcodes.
	 */
	public function auto_create_demo_page() {
		if ( ! get_option( 'sfpb_demo_page_created_v4' ) ) {
			$page_slug = 'social-feed-preview-demo';
			
			// Double check if page exists by slug
			$page_query = new \WP_Query(
				array(
					'name'        => $page_slug,
					'post_type'   => 'page',
					'post_status' => 'any',
					'numberposts' => 1,
				)
			);
			$page_exists = $page_query->have_posts();
			wp_reset_postdata();

			$page_content = '<!-- wp:html -->
<div class="sfpb-demo-showcase" style="font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif; color: #2d3748; padding: 10px 0; direction: ltr; text-align: left;">
    
    <!-- Hero Header -->
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #fff; padding: 45px 30px; border-radius: 20px; margin-bottom: 45px; text-align: center; box-shadow: 0 10px 30px rgba(79, 70, 229, 0.2);">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0 0 12px 0; color: #ffffff; letter-spacing: -0.02em;">Social Feed Preview Showcase</h1>
        <p style="font-size: 1.1rem; opacity: 0.95; margin: 0 auto; max-width: 650px; line-height: 1.6;">
            Explore responsive, high-performance mock social proof preview templates. Lightweight and fully optimized.
        </p>
    </div>

    <!-- Section 1 -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 30px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: visible;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px;">
            <span style="background: #e0e7ff; color: #4f46e5; padding: 5px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;">Template 01</span>
            <h2 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #1a202c; display: inline-block;">Default 3-Column Grid</h2>
        </div>
        <p style="color: #4a5568; font-size: 0.95rem; margin-top: 0; margin-bottom: 25px; line-height: 1.6;">
            Displays the latest 6 feed items in a responsive 3-column layout. Perfect for general feed walls.
        </p>
        [social_feed_preview limit="6" columns="3" full_width="yes"]
    </div>

    <!-- Section 2 -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 30px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: visible;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px;">
            <span style="background: #fdf2f8; color: #db2777; padding: 5px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;">Template 02</span>
            <h2 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #1a202c; display: inline-block;">Instagram Media Feed</h2>
        </div>
        <p style="color: #4a5568; font-size: 0.95rem; margin-top: 0; margin-bottom: 25px; line-height: 1.6;">
            Filters and displays the latest 9 items from Instagram, showcasing the media-centric layout.
        </p>
        [social_feed_preview platform="instagram" limit="9" full_width="yes"]
    </div>

    <!-- Section 3 -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 30px; margin-bottom: 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: visible;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px;">
            <span style="background: #f0fdf4; color: #16a34a; padding: 5px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;">Template 03</span>
            <h2 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #1a202c; display: inline-block;">Demo Campaign Grid (2 Columns)</h2>
        </div>
        <p style="color: #4a5568; font-size: 0.95rem; margin-top: 0; margin-bottom: 25px; line-height: 1.6;">
            Filters items associated with the <code>sfpb-demo</code> campaign name and renders them in 2 columns.
        </p>
        [social_feed_preview campaign="sfpb-demo" columns="2" full_width="yes"]
    </div>

    <!-- Section 4 -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 30px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); overflow: visible;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px; border-bottom: 1px solid #edf2f7; padding-bottom: 12px;">
            <span style="background: #fff7ed; color: #ea580c; padding: 5px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block;">Template 04</span>
            <h2 style="font-size: 1.4rem; font-weight: 700; margin: 0; color: #1a202c; display: inline-block;">YouTube Video Showcase</h2>
        </div>
        <p style="color: #4a5568; font-size: 0.95rem; margin-top: 0; margin-bottom: 25px; line-height: 1.6;">
            Filters and displays video mockup templates for YouTube. Perfect for video portfolios.
        </p>
        [social_feed_preview content_type="video" platform="youtube" full_width="yes"]
    </div>

</div>
<!-- /wp:html -->';

			if ( ! $page_exists ) {
				$page_data = array(
					'post_title'     => __( 'Social Feed Preview Demo', 'social-feed-preview-builder' ),
					'post_content'   => $page_content,
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_name'      => $page_slug,
					'comment_status' => 'closed',
				);

				wp_insert_post( $page_data );
			} else {
				// Update existing page content
				$existing_page = get_page_by_path( $page_slug );
				if ( $existing_page ) {
					wp_update_post(
						array(
							'ID'           => $existing_page->ID,
							'post_content' => $page_content,
						)
					);
				}
			}

			update_option( 'sfpb_demo_page_created_v4', '1' );
		}
	}
}
