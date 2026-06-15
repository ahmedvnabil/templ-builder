<?php
/**
 * Frontend grid template layout.
 *
 * @var \WP_Query $query
 * @var string $cols_class
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
		$accent = ! empty( $data['accent_color'] ) ? $data['accent_color'] : 'var(--tb-accent)';
		?>
		<div class="tb-card tb-card--grid" style="--tb-accent: <?php echo esc_attr( $accent ); ?>;">
			<?php if ( ! empty( $data['badge'] ) ) : ?>
				<div class="tb-badge"><?php echo esc_html( $data['badge'] ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $data['image'] ) ) : ?>
				<div class="tb-card__media">
					<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['image_alt'] ); ?>" loading="lazy">
				</div>
			<?php endif; ?>

			<div class="tb-card__body">
				<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
					<div class="tb-card__eyebrow"><?php echo esc_html( $data['eyebrow'] ); ?></div>
				<?php endif; ?>

				<h3 class="tb-card__title"><?php echo esc_html( $data['title'] ); ?></h3>

				<?php if ( ! empty( $data['subtitle'] ) ) : ?>
					<div class="tb-card__subtitle"><?php echo esc_html( $data['subtitle'] ); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $data['rating'] ) ) : ?>
					<div class="tb-testimonial__rating">
						<?php echo tb_render_stars( $data['rating'] ); ?>
					</div>
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

				<?php if ( ! empty( $data['external_url'] ) || ! empty( $data['secondary_url'] ) ) : ?>
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
