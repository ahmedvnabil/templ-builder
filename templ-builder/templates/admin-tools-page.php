<?php
/**
 * Admin Tools page template.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check active terms
$types       = get_terms( array( 'taxonomy' => 'templ_type', 'hide_empty' => false ) );
$collections = get_terms( array( 'taxonomy' => 'templ_collection', 'hide_empty' => false ) );
$templates   = tb_get_templates();

// Notices processing
$notice = isset( $_GET['tb_notice'] ) ? sanitize_key( $_GET['tb_notice'] ) : '';
$error  = isset( $_GET['tb_error'] ) ? sanitize_key( $_GET['tb_error'] ) : '';
$count  = isset( $_GET['count'] ) ? absint( $_GET['count'] ) : 0;
?>

<div class="wrap tb-tools-wrap">
	<div class="tb-hero">
		<h1><?php esc_html_e( 'Templ Builder Tools', 'templ-builder' ); ?></h1>
		<p><?php esc_html_e( 'Develop structured landing sections, portfolios, testimonials, and directories visually using shortcodes.', 'templ-builder' ); ?></p>
	</div>

	<?php if ( ! empty( $notice ) ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				if ( 'demo_generated' === $notice ) {
					printf( esc_html__( 'Successfully seeded %d beautiful use-case demo content items.', 'templ-builder' ), $count );
				} elseif ( 'demo_deleted' === $notice ) {
					printf( esc_html__( 'Cleared %d demo post elements from your database.', 'templ-builder' ), $count );
				} elseif ( 'imported' === $notice ) {
					printf( esc_html__( 'Import completed. Successfully created %d new Templ Items.', 'templ-builder' ), $count );
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $error ) ) : ?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php
				if ( 'unauthorized' === $error ) {
					esc_html_e( 'Error: You do not have sufficient permissions to execute this task.', 'templ-builder' );
				} elseif ( 'upload_error' === $error ) {
					esc_html_e( 'Error: Upload failed or invalid temporary file path provided.', 'templ-builder' );
				} elseif ( 'invalid_format' === $error ) {
					esc_html_e( 'Error: The imported JSON file is invalid or does not match the Templ Builder schema.', 'templ-builder' );
				} elseif ( 'empty_data' === $error ) {
					esc_html_e( 'Error: The provided backup package contains no template items.', 'templ-builder' );
				} else {
					esc_html_e( 'Error: Something went wrong while processing the import file.', 'templ-builder' );
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="tb-admin-card">
		<h2><span class="dashicons dashicons-editor-expand"></span> <?php esc_html_e( 'Live Visual Shortcode Builder', 'templ-builder' ); ?></h2>
		<p><?php esc_html_e( 'Adjust the fields below to dynamically generate a clean shortcode that you can embed anywhere.', 'templ-builder' ); ?></p>

		<div class="tb-meta-row tb-meta-row--three">
			<div class="tb-meta-field">
				<label for="tb_builder_template"><?php esc_html_e( 'Layout Template', 'templ-builder' ); ?></label>
				<select id="tb_builder_template" class="tb-input tb-builder-input">
					<option value=""><?php esc_html_e( '— Use Setting Default —', 'templ-builder' ); ?></option>
					<?php foreach ( $templates as $k => $lbl ) : ?>
						<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $lbl ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="tb-meta-field">
				<label for="tb_builder_type"><?php esc_html_e( 'Filter by Type', 'templ-builder' ); ?></label>
				<select id="tb_builder_type" class="tb-input tb-builder-input">
					<option value=""><?php esc_html_e( '— All Types —', 'templ-builder' ); ?></option>
					<?php if ( ! empty( $types ) && ! is_wp_error( $types ) ) : ?>
						<?php foreach ( $types as $t ) : ?>
							<option value="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>

			<div class="tb-meta-field">
				<label for="tb_builder_collection"><?php esc_html_e( 'Filter by Collection', 'templ-builder' ); ?></label>
				<select id="tb_builder_collection" class="tb-input tb-builder-input">
					<option value=""><?php esc_html_e( '— All Collections —', 'templ-builder' ); ?></option>
					<?php if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) : ?>
						<?php foreach ( $collections as $c ) : ?>
							<option value="<?php echo esc_attr( $c->slug ); ?>"><?php echo esc_html( $c->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>

		<div class="tb-meta-row tb-meta-row--three">
			<div class="tb-meta-field">
				<label for="tb_builder_limit"><?php esc_html_e( 'Query Limit', 'templ-builder' ); ?></label>
				<input type="number" id="tb_builder_limit" class="tb-input tb-builder-input" placeholder="6" min="1">
			</div>

			<div class="tb-meta-field">
				<label for="tb_builder_columns"><?php esc_html_e( 'Grid Columns (Desktop)', 'templ-builder' ); ?></label>
				<input type="number" id="tb_builder_columns" class="tb-input tb-builder-input" placeholder="3" min="1" max="6">
			</div>

			<div class="tb-meta-field">
				<label for="tb_builder_status"><?php esc_html_e( 'Filter by Status', 'templ-builder' ); ?></label>
				<select id="tb_builder_status" class="tb-input tb-builder-input">
					<option value="active"><?php esc_html_e( 'Active (Default)', 'templ-builder' ); ?></option>
					<option value="featured"><?php esc_html_e( 'Featured', 'templ-builder' ); ?></option>
					<option value="coming-soon"><?php esc_html_e( 'Coming Soon', 'templ-builder' ); ?></option>
					<option value="draft"><?php esc_html_e( 'Draft', 'templ-builder' ); ?></option>
					<option value=""><?php esc_html_e( '— All Statuses —', 'templ-builder' ); ?></option>
				</select>
			</div>
		</div>

		<div class="tb-meta-row tb-meta-row--three">
			<div class="tb-meta-field">
				<label for="tb_builder_orderby"><?php esc_html_e( 'Order By', 'templ-builder' ); ?></label>
				<select id="tb_builder_orderby" class="tb-input tb-builder-input">
					<option value=""><?php esc_html_e( '— Default —', 'templ-builder' ); ?></option>
					<option value="date"><?php esc_html_e( 'Published Date', 'templ-builder' ); ?></option>
					<option value="title"><?php esc_html_e( 'Title', 'templ-builder' ); ?></option>
					<option value="menu_order"><?php esc_html_e( 'Priority', 'templ-builder' ); ?></option>
					<option value="rand"><?php esc_html_e( 'Randomize', 'templ-builder' ); ?></option>
				</select>
			</div>

			<div class="tb-meta-field">
				<label for="tb_builder_order"><?php esc_html_e( 'Sort Order', 'templ-builder' ); ?></label>
				<select id="tb_builder_order" class="tb-input tb-builder-input">
					<option value="DESC"><?php esc_html_e( 'Descending (DESC)', 'templ-builder' ); ?></option>
					<option value="ASC"><?php esc_html_e( 'Ascending (ASC)', 'templ-builder' ); ?></option>
				</select>
			</div>

			<div class="tb-meta-field">
				<label for="tb_builder_class"><?php esc_html_e( 'Extra CSS Class', 'templ-builder' ); ?></label>
				<input type="text" id="tb_builder_class" class="tb-input tb-builder-input" placeholder="e.g. my-custom-section">
			</div>
		</div>

		<div class="tb-meta-row">
			<div class="tb-meta-field" style="justify-content:center;">
				<label>
					<input type="checkbox" id="tb_builder_featured" class="tb-builder-input">
					<strong><?php esc_html_e( 'Query Only Featured Items', 'templ-builder' ); ?></strong>
				</label>
			</div>
		</div>

		<div class="tb-code-block">
			<code id="tb_builder_output">[templ]</code>
			<button type="button" class="tb-copy-btn" id="tb_builder_copy_btn"><?php esc_html_e( 'Copy to Clipboard', 'templ-builder' ); ?></button>
		</div>
	</div>

	<?php if ( '1' === tb_get_setting( 'enable_demo_tools' ) ) : ?>
		<div class="tb-admin-card">
			<h2><span class="dashicons dashicons-welcome-add-page"></span> <?php esc_html_e( 'Demo Content Seeder', 'templ-builder' ); ?></h2>
			<p><?php esc_html_e( 'Populate your database with 9 fully pre-configured items demonstrating testimonials, tool directories, split sections, and portfolio grids.', 'templ-builder' ); ?></p>
			
			<div class="tb-meta-row" style="margin-top: 15px;">
				<div class="tb-meta-field">
					<form method="post" action="">
						<?php wp_nonce_field( 'tb_tools_nonce' ); ?>
						<input type="hidden" name="tb_action" value="generate_demo">
						<button type="submit" class="button button-primary" style="background-color: var(--tb-admin-success); border-color: var(--tb-admin-success); text-shadow:none; box-shadow:none;">
							<?php esc_html_e( 'Generate Demo Items', 'templ-builder' ); ?>
						</button>
					</form>
				</div>
				<div class="tb-meta-field">
					<form method="post" action="">
						<?php wp_nonce_field( 'tb_tools_nonce' ); ?>
						<input type="hidden" name="tb_action" value="delete_demo">
						<button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete all demo items?', 'templ-builder' ); ?>');">
							<?php esc_html_e( 'Delete Demo Items', 'templ-builder' ); ?>
						</button>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="tb-admin-card">
		<h2><span class="dashicons dashicons-backup"></span> <?php esc_html_e( 'JSON Import & Export Backup', 'templ-builder' ); ?></h2>
		<p><?php esc_html_e( 'Transfer your layouts, metadata structures, and assignments to another staging environment.', 'templ-builder' ); ?></p>
		
		<div class="tb-meta-row" style="margin-top: 20px; align-items: start;">
			<div class="tb-meta-field" style="border-right: 1px solid #ccd0d4; padding-right: 20px;">
				<h3><?php esc_html_e( 'Export Backup File', 'templ-builder' ); ?></h3>
				<form method="post" action="">
					<?php wp_nonce_field( 'tb_tools_nonce' ); ?>
					<input type="hidden" name="tb_action" value="export_json">
					<button type="submit" class="button button-secondary">
						<?php esc_html_e( 'Download Backup JSON', 'templ-builder' ); ?>
					</button>
				</form>
			</div>

			<div class="tb-meta-field" style="padding-left: 20px;">
				<h3><?php esc_html_e( 'Import Backup File', 'templ-builder' ); ?></h3>
				<form method="post" enctype="multipart/form-data" action="">
					<?php wp_nonce_field( 'tb_tools_nonce' ); ?>
					<input type="hidden" name="tb_action" value="import_json">
					<div class="tb-url-picker" style="margin-bottom: 12px;">
						<input type="file" name="tb_import_file" required style="border:1px solid #ccd0d4; padding:5px; border-radius:4px; width:100%;">
					</div>
					<div style="margin-bottom: 15px;">
						<label>
							<input type="checkbox" name="tb_publish_imported" value="1">
							<strong><?php esc_html_e( 'Publish imported items immediately (Draft by default)', 'templ-builder' ); ?></strong>
						</label>
					</div>
					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Upload and Restore Backup', 'templ-builder' ); ?>
					</button>
				</form>
			</div>
		</div>
	</div>

	<?php if ( '1' === tb_get_setting( 'enable_static_export' ) ) : ?>
		<div class="tb-admin-card">
			<h2><span class="dashicons dashicons-html"></span> <?php esc_html_e( 'Static HTML Snippet Exporter', 'templ-builder' ); ?></h2>
			<p><?php esc_html_e( 'Export responsive raw HTML code modules for static page usage.', 'templ-builder' ); ?></p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'tb_tools_nonce' ); ?>
				<input type="hidden" name="tb_action" value="static_html_export">
				
				<div class="tb-meta-row tb-meta-row--three" style="margin-top: 15px;">
					<div class="tb-meta-field">
						<label for="tb_export_template"><?php esc_html_e( 'Select Style Template', 'templ-builder' ); ?></label>
						<select name="tb_export_template" id="tb_export_template" class="tb-input">
							<option value="all"><?php esc_html_e( 'All Templates', 'templ-builder' ); ?></option>
							<?php foreach ( $templates as $k => $lbl ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $lbl ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="tb-meta-field">
						<label for="tb_export_type"><?php esc_html_e( 'Filter by Type', 'templ-builder' ); ?></label>
						<select name="tb_export_type" id="tb_export_type" class="tb-input">
							<option value="all"><?php esc_html_e( 'All Types', 'templ-builder' ); ?></option>
							<?php if ( ! empty( $types ) && ! is_wp_error( $types ) ) : ?>
								<?php foreach ( $types as $t ) : ?>
									<option value="<?php echo esc_attr( $t->slug ); ?>"><?php echo esc_html( $t->name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>

					<div class="tb-meta-field">
						<label for="tb_export_collection"><?php esc_html_e( 'Filter by Collection', 'templ-builder' ); ?></label>
						<select name="tb_export_collection" id="tb_export_collection" class="tb-input">
							<option value="all"><?php esc_html_e( 'All Collections', 'templ-builder' ); ?></option>
							<?php if ( ! empty( $collections ) && ! is_wp_error( $collections ) ) : ?>
								<?php foreach ( $collections as $c ) : ?>
									<option value="<?php echo esc_attr( $c->slug ); ?>"><?php echo esc_html( $c->name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				</div>

				<div class="tb-meta-row">
					<div class="tb-meta-field">
						<label for="tb_export_limit"><?php esc_html_e( 'Export Query Limit', 'templ-builder' ); ?></label>
						<input type="number" name="tb_export_limit" id="tb_export_limit" class="tb-input" value="6" min="1">
					</div>
					<div class="tb-meta-field" style="justify-content: flex-end; margin-bottom: 5px;">
						<button type="submit" class="button button-primary" style="background-color: var(--tb-admin-primary); border-color: var(--tb-admin-primary); text-shadow:none; box-shadow:none;">
							<?php esc_html_e( 'Download Static HTML Snippet File', 'templ-builder' ); ?>
						</button>
					</div>
				</div>
			</form>
		</div>
	<?php endif; ?>
</div>
