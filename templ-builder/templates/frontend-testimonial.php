<?php
/**
 * Frontend testimonial template layout.
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
		<div class="tb-testimonial" style="--tb-accent: <?php echo esc_attr( $accent ); ?>; position: relative;">
			<?php if ( ! empty( $data['badge'] ) ) : ?>
				<div class="tb-badge" style="position: absolute; top: 16px; right: 16px;"><?php echo esc_html( $data['badge'] ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $data['rating'] ) ) : ?>
				<div class="tb-testimonial__rating" style="margin-bottom: 15px;">
					<?php echo tb_render_stars( $data['rating'] ); ?>
				</div>
			<?php endif; ?>

			<div class="tb-testimonial__quote" style="font-size: 15px; line-height: 1.6; font-style: italic; color: #374151; margin-bottom: 20px;">
				&ldquo;<?php echo esc_html( ! empty( $data['short_description'] ) ? $data['short_description'] : wp_strip_all_tags( $data['content'] ) ); ?>&rdquo;
			</div>

			<div class="tb-testimonial__meta">
				<?php if ( ! empty( $data['image'] ) ) : ?>
					<div class="tb-testimonial__avatar">
						<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['title'] ); ?>" loading="lazy">
					</div>
				<?php else : ?>
					<div class="tb-testimonial__avatar" style="background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:var(--tb-muted); font-size:20px;">
						<span class="dashicons dashicons-admin-users"></span>
					</div>
				<?php endif; ?>

				<div class="tb-testimonial__author" style="line-height: 1.3;">
					<span class="tb-testimonial__name" style="font-weight: 700; display: block; font-size: 14px; color: var(--tb-text);"><?php echo esc_html( $data['title'] ); ?></span>
					<span class="tb-testimonial__title" style="font-size: 12px; color: var(--tb-muted);">
						<?php echo esc_html( $data['role_label'] ); ?>
						<?php if ( ! empty( $data['organization'] ) ) echo ' &bull; ' . esc_html( $data['organization'] ); ?>
					</span>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
</div>
