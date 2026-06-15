<?php
/**
 * Frontend list template layout.
 *
 * @var \WP_Query $query
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$renderer = \TB\includes\TB_Renderer::get_instance();
?>

<div class="tb-list">
	<?php
	while ( $query->have_posts() ) :
		$query->the_post();
		$data = $renderer->normalize_post_data( get_post() );
		$accent = ! empty( $data['accent_color'] ) ? $data['accent_color'] : 'var(--tb-accent)';
		?>
		<div class="tb-list__item" style="border-inline-start: 4px solid <?php echo esc_attr( $accent ); ?>;">
			<?php if ( ! empty( $data['image'] ) ) : ?>
				<div class="tb-list__media">
					<img src="<?php echo esc_url( $data['image'] ); ?>" alt="<?php echo esc_attr( $data['image_alt'] ); ?>" loading="lazy">
				</div>
			<?php elseif ( ! empty( $data['icon'] ) ) : ?>
				<div class="tb-list__media" style="display:flex; align-items:center; justify-content:center; background:#f3f4f6; color:<?php echo esc_attr( $accent ); ?>; font-size:32px;">
					<span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>" style="font-size:inherit; width:auto; height:auto;"></span>
				</div>
			<?php endif; ?>
			
			<div style="flex-grow: 1; display:flex; flex-direction:column; gap:4px;">
				<div style="display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
					<h4 style="margin:0; font-size:16px; font-weight:700; color:var(--tb-text);"><?php echo esc_html( $data['title'] ); ?></h4>
					<?php if ( ! empty( $data['badge'] ) ) : ?>
						<span class="tb-badge" style="position:static; padding:2px 8px; font-size:9px;"><?php echo esc_html( $data['badge'] ); ?></span>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $data['subtitle'] ) ) : ?>
					<span style="font-size:12px; color:var(--tb-muted);"><?php echo esc_html( $data['subtitle'] ); ?></span>
				<?php endif; ?>
				<div style="font-size:13px; line-height:1.5; color:#4b5563; margin-top:2px;">
					<?php 
					if ( ! empty( $data['short_description'] ) ) {
						echo esc_html( $data['short_description'] );
					} else {
						echo esc_html( wp_trim_words( $data['content'], 18 ) );
					}
					?>
				</div>
			</div>

			<?php if ( ! empty( $data['external_url'] ) ) : ?>
				<div style="flex-shrink:0;">
					<a href="<?php echo esc_url( $data['external_url'] ); ?>" class="tb-button" style="padding: 8px 14px; font-size:12px;">
						<?php echo esc_html( ! empty( $data['button_label'] ) ? $data['button_label'] : __( 'View', 'templ-builder' ) ); ?> &rarr;
					</a>
				</div>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>
