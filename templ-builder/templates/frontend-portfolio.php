<?php
/**
 * Frontend portfolio card template layout.
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
		<div class="tb-card tb-card--portfolio" style="--tb-accent: <?php echo esc_attr( $accent ); ?>; height: 100%;">
			<?php if ( ! empty( $data['badge'] ) ) : ?>
				<div class="tb-badge"><?php echo esc_html( $data['badge'] ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $data['image'] ) ) : ?>
				<div class="tb-card__media" style="aspect-ratio: 4/3;">
					<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['image_alt'] ); ?>" loading="lazy">
				</div>
			<?php endif; ?>

			<div class="tb-card__body" style="display: flex; flex-direction: column; height: calc(100% - 200px);">
				<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
					<div class="tb-card__eyebrow"><?php echo esc_html( $data['eyebrow'] ); ?></div>
				<?php endif; ?>

				<h3 class="tb-card__title"><?php echo esc_html( $data['title'] ); ?></h3>

				<?php if ( ! empty( $data['subtitle'] ) ) : ?>
					<div class="tb-card__subtitle" style="margin-bottom: 8px;"><?php echo esc_html( $data['subtitle'] ); ?></div>
				<?php endif; ?>

				<div class="tb-card__text" style="font-size:13px; color:#4b5563; line-height:1.5; margin-bottom:12px; flex-grow: 1;">
					<?php 
					if ( ! empty( $data['short_description'] ) ) {
						echo esc_html( $data['short_description'] );
					} else {
						echo esc_html( wp_trim_words( $data['content'], 18 ) );
					}
					?>
				</div>

				<!-- Portfolio Stack / Results -->
				<?php if ( ! empty( $data['custom'] ) ) : ?>
					<div class="tb-portfolio-meta" style="margin-top:auto; font-size:12px; border-top:1px dashed var(--tb-border); padding-top:10px; margin-bottom:15px; color:#4b5563;">
						<?php if ( ! empty( $data['custom']['tech_stack'] ) ) : ?>
							<div style="margin-bottom:4px;">
								<strong><?php esc_html_e( 'Stack:', 'templ-builder' ); ?></strong>
								<?php echo $renderer->render_custom_field_safely( $data['custom']['tech_stack'] ); ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $data['custom']['result'] ) ) : ?>
							<div>
								<strong><?php esc_html_e( 'Result:', 'templ-builder' ); ?></strong>
								<?php echo $renderer->render_custom_field_safely( $data['custom']['result'] ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $data['external_url'] ) ) : ?>
					<div class="tb-card__footer" style="margin-top:auto;">
						<a href="<?php echo esc_url( $data['external_url'] ); ?>" class="tb-button" style="width:100%; text-align:center;">
							<?php echo esc_html( ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'View Case Study', 'templ-builder' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endwhile; ?>
</div>
