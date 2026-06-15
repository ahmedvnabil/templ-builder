<?php
/**
 * Frontend card template layout.
 *
 * @var \WP_Query $query
 * @var string $cols_class
 * @var string $template_key
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$renderer = \TB\includes\TB_Renderer::get_instance();
?>

<div class="<?php echo esc_attr( $cols_class ); ?>">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$data = $renderer->normalize_post_data( get_post() );
		$item_template = ! empty( $data['template_key'] ) ? $data['template_key'] : $template_key;
		$accent = ! empty( $data['accent_color'] ) ? $data['accent_color'] : 'var(--tb-accent)';
		?>
		<div class="tb-card tb-card--<?php echo esc_attr( $item_template ); ?>" style="--tb-accent: <?php echo esc_attr( $accent ); ?>;">
			<?php if ( ! empty( $data['badge'] ) ) : ?>
				<div class="tb-badge"><?php echo esc_html( $data['badge'] ); ?></div>
			<?php endif; ?>

			<?php if ( 'minimal' !== $item_template && 'service-card' !== $item_template && 'social-proof' !== $item_template && ! empty( $data['image'] ) ) : ?>
				<div class="tb-card__media">
					<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['image_alt'] ); ?>" loading="lazy">
				</div>
			<?php endif; ?>

			<div class="tb-card__body">
				<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
					<div class="tb-card__eyebrow"><?php echo esc_html( $data['eyebrow'] ); ?></div>
				<?php endif; ?>

				<?php if ( 'social-proof' !== $item_template ) : ?>
					<h3 class="tb-card__title"><?php echo esc_html( $data['title'] ); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( $data['subtitle'] ) && 'social-proof' !== $item_template ) : ?>
					<div class="tb-card__subtitle"><?php echo esc_html( $data['subtitle'] ); ?></div>
				<?php endif; ?>

				<?php if ( 'app-card' === $item_template ) : ?>
					<div class="tb-app-header" style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
						<?php if ( ! empty( $data['icon'] ) ) : ?>
							<div class="tb-app-icon" style="font-size:28px; background:#f3f4f6; padding:8px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; color:<?php echo esc_attr( $accent ); ?>;">
								<span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:inherit; width:auto; height:auto;"></span>
							</div>
						<?php endif; ?>
						<div class="tb-app-details" style="display:flex; flex-direction:column;">
							<?php if ( ! empty( $data['custom']['tech_stack'] ) ) : ?>
								<div class="tb-app-stack" style="font-size:12px; color:var(--tb-muted);">
									<?php echo $renderer->render_custom_field_safely( $data['custom']['tech_stack'] ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					
					<?php if ( ! empty( $data['price'] ) ) : ?>
						<div class="tb-app-price" style="font-size:15px; font-weight:700; margin-bottom:10px; color:<?php echo esc_attr( $accent ); ?>;">
							<?php echo esc_html( $data['price'] ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( 'service-card' === $item_template ) : ?>
					<?php if ( ! empty( $data['price'] ) ) : ?>
						<div class="tb-service-price" style="font-size:28px; font-weight:800; color:<?php echo esc_attr( $accent ); ?>; margin-bottom:12px;">
							<?php echo esc_html( $data['price'] ); ?>
						</div>
					<?php endif; ?>
					
					<?php if ( ! empty( $data['custom']['benefits'] ) ) : ?>
						<div class="tb-service-benefits" style="font-size:13px; margin-bottom:15px; color:#4b5563;">
							<?php echo $renderer->render_custom_field_safely( $data['custom']['benefits'] ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( 'social-proof' === $item_template ) : ?>
					<?php if ( ! empty( $data['rating'] ) ) : ?>
						<div class="tb-testimonial__rating" style="margin-bottom:10px;">
							<?php echo tb_render_stars( $data['rating'] ); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<div class="tb-card__text">
					<?php 
					if ( ! empty( $data['short_description'] ) ) {
						echo esc_html( $data['short_description'] );
					} else {
						echo esc_html( wp_trim_words( $data['content'], 25 ) );
					}
					?>
				</div>

				<?php if ( 'social-proof' === $item_template ) : ?>
					<div class="tb-testimonial__meta" style="margin-top:auto; display:flex; align-items:center; gap:10px; border-top:1px solid var(--tb-border); padding-top:10px;">
						<?php if ( ! empty( $data['image'] ) ) : ?>
							<div class="tb-testimonial__avatar" style="width:36px; height:36px; border-radius:50%; overflow:hidden;">
								<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['title'] ); ?>" style="width:100%; height:100%; object-fit:cover;">
							</div>
						<?php else : ?>
							<div class="tb-testimonial__avatar" style="width:36px; height:36px; border-radius:50%; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:var(--tb-muted);">
								<span class="dashicons dashicons-admin-users"></span>
							</div>
						<?php endif; ?>
						<div class="tb-testimonial__author" style="display:flex; flex-direction:column; line-height:1.2;">
							<span class="tb-testimonial__name" style="font-weight:700; font-size:13px;"><?php echo esc_html( $data['title'] ); ?></span>
							<span class="tb-testimonial__title" style="font-size:11px; color:var(--tb-muted);">
								<?php echo esc_html( $data['role_label'] ); ?>
								<?php if ( ! empty( $data['organization'] ) ) echo ' @ ' . esc_html( $data['organization'] ); ?>
							</span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( 'app-card' !== $item_template && 'service-card' !== $item_template && 'social-proof' !== $item_template && 'minimal' !== $item_template && ! empty( $data['rating'] ) ) : ?>
					<div class="tb-testimonial__rating" style="margin-bottom:12px;">
						<?php echo tb_render_stars( $data['rating'] ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $data['custom'] ) && 'app-card' !== $item_template && 'service-card' !== $item_template && 'social-proof' !== $item_template ) : ?>
					<div class="tb-card-custom-fields" style="font-size:12px; margin-bottom:15px; border-top:1px dashed var(--tb-border); padding-top:10px;">
						<?php foreach ( $data['custom'] as $k => $v ) : ?>
							<div class="tb-custom-field-row" style="margin-bottom:6px;">
								<strong><?php echo esc_html( ucwords( str_replace( '_', ' ', $k ) ) ); ?>:</strong>
								<?php echo $renderer->render_custom_field_safely( $v ); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( 'social-proof' !== $item_template && 'minimal' !== $item_template && ( ! empty( $data['external_url'] ) || ! empty( $data['secondary_url'] ) ) ) : ?>
					<div class="tb-card__footer">
						<?php if ( ! empty( $data['external_url'] ) ) : ?>
							<a href="<?php echo esc_url( $data['external_url'] ); ?>" class="tb-button">
								<?php echo esc_html( ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'Learn More', 'templ-builder' ) ); ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $data['secondary_url'] ) ) : ?>
							<a href="<?php echo esc_url( $data['secondary_url'] ); ?>" class="tb-button tb-button--secondary">
								<?php echo esc_html( ! empty( $data['secondary_button_label'] ) ? $data['secondary_button_label'] : __( 'View Detail', 'templ-builder' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endwhile; ?>
</div>
