<?php
/**
 * Frontend section split template layout.
 *
 * @var \WP_Query $query
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$renderer = \TB\includes\TB_Renderer::get_instance();
?>

<div class="tb-sections-wrapper" style="display:flex; flex-direction:column; gap:60px;">
	<?php
	$index = 0;
	while ( $query->have_posts() ) :
		$query->the_post();
		$data = $renderer->normalize_post_data( get_post() );
		$accent = ! empty( $data['accent_color'] ) ? $data['accent_color'] : 'var(--tb-accent)';
		
		// Alternating sections on desktop
		$is_even = ( $index % 2 === 0 );
		$image_order = $is_even ? 'order: 2;' : 'order: 1;';
		$text_order  = $is_even ? 'order: 1;' : 'order: 2;';
		$index++;
		?>
		<div class="tb-section" style="--tb-accent: <?php echo esc_attr( $accent ); ?>;">
			<!-- Media Column -->
			<?php if ( ! empty( $data['image'] ) ) : ?>
				<div class="tb-section__media" style="<?php echo esc_attr( $image_order ); ?>">
					<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['image_alt'] ); ?>" loading="lazy">
				</div>
			<?php elseif ( ! empty( $data['icon'] ) ) : ?>
				<div class="tb-section__media" style="<?php echo esc_attr( $image_order ); ?> display:flex; align-items:center; justify-content:center; padding:50px; background:#f9fafb; color:<?php echo esc_attr( $accent ); ?>; font-size:80px; border-radius:var(--tb-radius); border:1px solid var(--tb-border); min-height:300px;">
					<span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:inherit; width:auto; height:auto;"></span>
				</div>
			<?php endif; ?>

			<!-- Content Column -->
			<div class="tb-section__content" style="<?php echo esc_attr( $text_order ); ?> display:flex; flex-direction:column; justify-content:center;">
				<?php if ( ! empty( $data['eyebrow'] ) ) : ?>
					<span class="tb-card__eyebrow" style="font-size:12px; margin-bottom:12px;"><?php echo esc_html( $data['eyebrow'] ); ?></span>
				<?php endif; ?>

				<h2 style="font-size:32px; font-weight:800; line-height:1.2; margin:0 0 12px 0; color:var(--tb-text);"><?php echo esc_html( $data['title'] ); ?></h2>

				<?php if ( ! empty( $data['subtitle'] ) ) : ?>
					<h3 style="font-size:18px; font-weight:500; color:var(--tb-muted); margin:0 0 20px 0; line-height:1.4;"><?php echo esc_html( $data['subtitle'] ); ?></h3>
				<?php endif; ?>

				<div class="tb-card__content" style="font-size:15px; line-height:1.7; color:#4b5563; margin-bottom:24px;">
					<?php echo wp_kses_post( wpautop( $data['content'] ) ); ?>
				</div>

				<?php if ( ! empty( $data['external_url'] ) || ! empty( $data['secondary_url'] ) ) : ?>
					<div class="tb-card__footer" style="gap:16px;">
						<?php if ( ! empty( $data['external_url'] ) ) : ?>
							<a href="<?php echo esc_url( $data['external_url'] ); ?>" class="tb-button" style="padding:12px 24px; font-size:14px; border-radius:10px;">
								<?php echo esc_html( ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'Get Started', 'templ-builder' ) ); ?>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $data['secondary_url'] ) ) : ?>
							<a href="<?php echo esc_url( $data['secondary_url'] ); ?>" class="tb-button tb-button--secondary" style="padding:12px 24px; font-size:14px; border-radius:10px;">
								<?php echo esc_html( ! empty( $data['secondary_button_label'] ) ? $data['secondary_button_label'] : __( 'Learn More', 'templ-builder' ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endwhile; ?>
</div>
