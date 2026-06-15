<?php
/**
 * Frontend single showcase template layout.
 *
 * @var \WP_Query $query
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$renderer = \TB\includes\TB_Renderer::get_instance();
?>

<div class="tb-single-item-wrapper">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$data = $renderer->normalize_post_data( get_post() );
		$accent = ! empty( $data['accent_color'] ) ? $data['accent_color'] : 'var(--tb-accent)';
		?>
		<div class="tb-single-item" style="--tb-accent: <?php echo esc_attr( $accent ); ?>; background:#ffffff; border:1px solid var(--tb-border); border-radius:var(--tb-radius); box-shadow:var(--tb-shadow); padding:40px; box-sizing:border-box; width: 100%;">
			<?php if ( ! empty( $data['image'] ) ) : ?>
				<div class="tb-single-item__media" style="border-radius:var(--tb-radius); overflow:hidden; margin-bottom:30px; box-shadow:var(--tb-shadow); max-height:450px; width:100%;">
					<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['image_alt'] ); ?>" style="width:100%; height:auto; object-fit:cover; display:block;" loading="lazy">
				</div>
			<?php endif; ?>

			<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; margin-bottom:15px;">
				<div style="display:flex; flex-direction:column; text-align: start;">
					<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
						<span class="tb-card__eyebrow" style="font-size:12px; margin-bottom:8px;"><?php echo esc_html( $data['eyebrow'] ); ?></span>
					<?php endif; ?>
					<h1 style="font-size:36px; font-weight:800; color:var(--tb-text); margin:0; line-height:1.2;"><?php echo esc_html( $data['title'] ); ?></h1>
					<?php if ( ! empty( $data['subtitle'] ) ) : ?>
						<h3 style="font-size:20px; font-weight:400; color:var(--tb-muted); margin:6px 0 0 0; line-height:1.4;"><?php echo esc_html( $data['subtitle'] ); ?></h3>
					<?php endif; ?>
				</div>

				<div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
					<?php if ( ! empty( $data['badge'] ) ) : ?>
						<span class="tb-badge" style="position:static; padding:6px 14px; font-size:11px;"><?php echo esc_html( $data['badge'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $data['price'] ) ) : ?>
						<span style="font-size:22px; font-weight:800; color:<?php echo esc_attr( $accent ); ?>;"><?php echo esc_html( $data['price'] ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<!-- Meta items bar -->
			<?php if ( ! empty( $data['rating'] ) || ! empty( $data['role_label'] ) || ! empty( $data['organization'] ) || ! empty( $data['location_label'] ) ) : ?>
				<div class="tb-single-meta-bar" style="display:flex; gap:24px; flex-wrap:wrap; border-top:1px solid var(--tb-border); border-bottom:1px solid var(--tb-border); padding:15px 0; margin-bottom:30px; font-size:14px; color:#4b5563; text-align: start;">
					<?php if ( ! empty( $data['rating'] ) ) : ?>
						<div>
							<strong><?php esc_html_e( 'Rating:', 'templ-builder' ); ?></strong>
							<?php echo tb_render_stars( $data['rating'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $data['role_label'] ) ) : ?>
						<div>
							<strong><?php esc_html_e( 'Role:', 'templ-builder' ); ?></strong>
							<?php echo esc_html( $data['role_label'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $data['organization'] ) ) : ?>
						<div>
							<strong><?php esc_html_e( 'Organization:', 'templ-builder' ); ?></strong>
							<?php echo esc_html( $data['organization'] ); ?>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $data['location_label'] ) ) : ?>
						<div>
							<strong><?php esc_html_e( 'Location:', 'templ-builder' ); ?></strong>
							<?php echo esc_html( $data['location_label'] ); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="tb-single-item__content" style="font-size:16px; line-height:1.8; color:#374151; margin-bottom:30px; text-align: start;">
				<?php echo wp_kses_post( wpautop( $data['content'] ) ); ?>
			</div>

			<!-- Custom fields table -->
			<?php if ( ! empty( $data['custom'] ) ) : ?>
				<div class="tb-single-custom-fields" style="margin-bottom:30px; border-top:1px dashed var(--tb-border); padding-top:20px; text-align: start;">
					<h3 style="font-size:18px; font-weight:700; margin-top:0; margin-bottom:15px;"><?php esc_html_e( 'Structured Parameters', 'templ-builder' ); ?></h3>
					<table class="tb-custom-table" style="width:100%; border-collapse:collapse; text-align:inherit;">
						<?php foreach ( $data['custom'] as $k => $v ) : ?>
							<tr style="border-bottom:1px solid var(--tb-border);">
								<th style="padding:10px; font-weight:700; width:250px; background:#f9fafb; text-align: inherit;"><?php echo esc_html( ucwords( str_replace( '_', ' ', $k ) ) ); ?></th>
								<td style="padding:10px;"><?php echo $renderer->render_custom_field_safely( $v ); ?></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $data['external_url'] ) || ! empty( $data['secondary_url'] ) ) : ?>
				<div style="display:flex; gap:16px; flex-wrap:wrap; border-top:1px solid var(--tb-border); padding-top:25px; justify-content: flex-start;">
					<?php if ( ! empty( $data['external_url'] ) ) : ?>
						<a href="<?php echo esc_url( $data['external_url'] ); ?>" class="tb-button" style="padding:12px 28px; font-size:14px; border-radius:8px;">
							<?php echo esc_html( ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'Visit Website', 'templ-builder' ) ); ?>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $data['secondary_url'] ) ) : ?>
						<a href="<?php echo esc_url( $data['secondary_url'] ); ?>" class="tb-button tb-button--secondary" style="padding:12px 28px; font-size:14px; border-radius:8px;">
							<?php echo esc_html( ! empty( $data['secondary_button_label'] ) ? $data['secondary_button_label'] : __( 'Documentation', 'templ-builder' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>
