<?php
/**
 * Frontend feature template layout.
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
		<div class="tb-card tb-card--feature" style="--tb-accent: <?php echo esc_attr( $accent ); ?>; padding: 30px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; height: 100%;">
			<?php if ( ! empty( $data['icon'] ) ) : ?>
				<div class="tb-feature__icon" style="width: 60px; height: 60px; border-radius: 50%; background: <?php echo esc_attr( $accent ); ?>12; display: flex; align-items: center; justify-content: center; color: <?php echo esc_attr( $accent ); ?>; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
					<span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:inherit; width:auto; height:auto;"></span>
				</div>
			<?php endif; ?>

			<h3 class="tb-card__title" style="font-size: 18px; margin: 0 0 10px 0; font-weight: 700;"><?php echo esc_html( $data['title'] ); ?></h3>
			
			<div class="tb-card__text" style="font-size: 14px; line-height: 1.6; color: #4b5563; margin-bottom: 0; flex-grow: 1;">
				<?php 
				if ( ! empty( $data['short_description'] ) ) {
					echo esc_html( $data['short_description'] );
				} else {
					echo esc_html( wp_trim_words( $data['content'], 20 ) );
				}
				?>
			</div>

			<?php if ( ! empty( $data['custom'] ) ) : ?>
				<div class="tb-feature-custom" style="font-size: 12px; margin-top: 15px; border-top: 1px dashed var(--tb-border); padding-top: 10px; width: 100%; text-align: left;">
					<?php foreach ( $data['custom'] as $k => $v ) : ?>
						<div style="margin-bottom:4px;">
							<strong><?php echo esc_html( ucwords( str_replace( '_', ' ', $k ) ) ); ?>:</strong>
							<?php echo $renderer->render_custom_field_safely( $v ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $data['external_url'] ) ) : ?>
				<a href="<?php echo esc_url( $data['external_url'] ); ?>" style="margin-top: 20px; font-size: 13px; font-weight: 600; text-decoration: none; color: <?php echo esc_attr( $accent ); ?>; display: inline-flex; align-items: center; gap: 4px;">
					<?php echo esc_html( ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'Learn More', 'templ-builder' ) ); ?> &rarr;
				</a>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>
