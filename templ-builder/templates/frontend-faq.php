<?php
/**
 * Frontend FAQ template layout.
 *
 * @var \WP_Query $query
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$renderer = \TB\includes\TB_Renderer::get_instance();
?>

<div class="tb-faqs-container">
	<noscript>
		<style>
			.tb-faq__answer {
				max-height: none !important;
			}
			.tb-faq__icon {
				display: none !important;
			}
		</style>
	</noscript>

	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$data = $renderer->normalize_post_data( get_post() );
		$accent = ! empty( $data['accent_color'] ) ? $data['accent_color'] : 'var(--tb-accent)';
		?>
		<div class="tb-faq" style="--tb-accent: <?php echo esc_attr( $accent ); ?>;">
			<button class="tb-faq__question" aria-expanded="false">
				<span><?php echo esc_html( $data['title'] ); ?></span>
				<span class="tb-faq__icon dashicons dashicons-arrow-down-alt2" style="color: <?php echo esc_attr( $accent ); ?>;"></span>
			</button>
			<div class="tb-faq__answer">
				<div class="tb-faq__answer-inner">
					<?php if ( ! empty( $data['short_description'] ) ) : ?>
						<p style="font-weight:600; margin-bottom:10px; color:var(--tb-text);"><?php echo esc_html( $data['short_description'] ); ?></p>
					<?php endif; ?>
					
					<?php echo wp_kses_post( wpautop( $data['content'] ) ); ?>

					<?php if ( ! empty( $data['custom'] ) ) : ?>
						<div style="font-size: 12px; margin-top: 15px; border-top: 1px dashed var(--tb-border); padding-top: 10px;">
							<?php foreach ( $data['custom'] as $k => $v ) : ?>
								<div style="margin-bottom:4px;">
									<strong><?php echo esc_html( ucwords( str_replace( '_', ' ', $k ) ) ); ?>:</strong>
									<?php echo $renderer->render_custom_field_safely( $v ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endwhile; ?>
</div>
