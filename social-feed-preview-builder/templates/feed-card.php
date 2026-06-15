<?php
/**
 * Single Feed Card Layout Template.
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Extract domain from external URL for link preview
$domain = '';
if ( ! empty( $external_url ) ) {
	$parse = wp_parse_url( $external_url );
	if ( ! empty( $parse['host'] ) ) {
		$domain = str_replace( 'www.', '', $parse['host'] );
	}
}

// Generate platform-specific indicator/badge helper with ARIA tags
$platform_badge = '';
switch ( $platform ) {
	case 'facebook':
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--facebook" role="img" aria-label="' . esc_attr__( 'Facebook Inspired Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'Facebook Inspired', 'social-feed-preview-builder' ) . '"></span>';
		break;
	case 'instagram':
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--instagram" role="img" aria-label="' . esc_attr__( 'Instagram Inspired Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'Instagram Inspired', 'social-feed-preview-builder' ) . '"></span>';
		break;
	case 'x':
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--x" role="img" aria-label="' . esc_attr__( 'X / Twitter Inspired Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'X / Twitter Inspired', 'social-feed-preview-builder' ) . '"></span>';
		break;
	case 'linkedin':
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--linkedin" role="img" aria-label="' . esc_attr__( 'LinkedIn Inspired Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'LinkedIn Inspired', 'social-feed-preview-builder' ) . '"></span>';
		break;
	case 'tiktok':
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--tiktok" role="img" aria-label="' . esc_attr__( 'TikTok Inspired Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'TikTok Inspired', 'social-feed-preview-builder' ) . '"></span>';
		break;
	case 'youtube':
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--youtube" role="img" aria-label="' . esc_attr__( 'YouTube Inspired Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'YouTube Inspired', 'social-feed-preview-builder' ) . '"></span>';
		break;
	default:
		$platform_badge = '<span class="sfpb-card__badge sfpb-card__badge--generic" role="img" aria-label="' . esc_attr__( 'Custom Layout', 'social-feed-preview-builder' ) . '" title="' . esc_attr__( 'Custom Platform', 'social-feed-preview-builder' ) . '"></span>';
		break;
}

// Define inline SVG icons
$like_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
$comment_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>';
$share_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8M16 6l-4-4-4 4M12 2v13"/></svg>';
$retweet_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14M7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>';
$views_icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
$play_icon = '<svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"/></svg>';
?>

<article class="sfpb-card sfpb-card--<?php echo esc_attr( $platform ); ?>" id="sfpb-card-<?php echo esc_attr( $id ); ?>" tabindex="0">
	
	<!-- Card Main Wrapper (especially needed for layout control) -->
	<div class="sfpb-card__inner">
		
		<!-- CARD HEADER -->
		<header class="sfpb-card__header">
			<div class="sfpb-card__header-left">
				<div class="sfpb-card__avatar-wrapper">
					<?php if ( ! empty( $author_avatar_url ) ) : ?>
						<img class="sfpb-card__avatar" src="<?php echo esc_url( $author_avatar_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Profile avatar of %s', 'social-feed-preview-builder' ), $author_name ) ); ?>" loading="lazy" />
					<?php else : ?>
						<?php echo $avatar_fallback; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
				<div class="sfpb-card__author-info">
					<div class="sfpb-card__author-name">
						<?php echo esc_html( $author_name ); ?>
						<?php echo $platform_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="sfpb-card__author-handle">
						<?php echo esc_html( $author_handle ); ?>
					</div>
				</div>
			</div>
			<div class="sfpb-card__header-right">
				<span class="sfpb-card__date"><?php echo esc_html( $post_date_label ); ?></span>
			</div>
		</header>

		<!-- CARD BODY (TEXT CONTENT) -->
		<?php if ( ! empty( $content ) ) : ?>
			<div class="sfpb-card__body">
				<div class="sfpb-card__text">
					<?php echo wp_kses_post( $content ); ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- CARD MEDIA (IMAGE / VIDEO / LINK PREVIEW) -->
		<?php if ( 'image' === $content_type && ! empty( $media_url ) ) : ?>
			<div class="sfpb-card__media sfpb-card__media--image">
				<img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Post image attachment by %s', 'social-feed-preview-builder' ), $author_name ) ); ?>" loading="lazy" />
			</div>
		<?php elseif ( 'video' === $content_type && ! empty( $video_url ) ) : ?>
			<div class="sfpb-card__media sfpb-card__media--video">
				<div class="sfpb-video-wrapper">
					<video src="<?php echo esc_url( $video_url ); ?>" controls preload="metadata" aria-label="<?php echo esc_attr( sprintf( __( 'Mockup video uploaded by %s', 'social-feed-preview-builder' ), $author_name ) ); ?>"></video>
					<div class="sfpb-video-overlay">
						<span class="sfpb-video-play-btn" aria-hidden="true"><?php echo $play_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
				</div>
			</div>
		<?php elseif ( 'link' === $content_type && ! empty( $external_url ) ) : ?>
			<div class="sfpb-card__media sfpb-card__media--link">
				<a href="<?php echo esc_url( $external_url ); ?>" target="_blank" rel="noopener" class="sfpb-link-preview-box" aria-label="<?php echo esc_attr( sprintf( __( 'External Link: %s - CTA %s', 'social-feed-preview-builder' ), $domain, $button_label ) ); ?>">
					<?php if ( ! empty( $media_url ) ) : ?>
						<div class="sfpb-link-preview-image">
							<img src="<?php echo esc_url( $media_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Link preview thumbnail: %s', 'social-feed-preview-builder' ), $button_label ) ); ?>" loading="lazy" />
						</div>
					<?php endif; ?>
					<div class="sfpb-link-preview-details">
						<?php if ( ! empty( $domain ) ) : ?>
							<span class="sfpb-link-preview-domain"><?php echo esc_html( $domain ); ?></span>
						<?php endif; ?>
						<h4 class="sfpb-link-preview-title"><?php echo esc_html( $author_name . ' - ' . $button_label ); ?></h4>
						<p class="sfpb-link-preview-desc">
							<?php 
							// Trim content for preview
							$plain_text = wp_strip_all_tags( $content );
							echo esc_html( wp_html_excerpt( $plain_text, 100, '...' ) );
							?>
						</p>
					</div>
					<?php if ( ! empty( $button_label ) ) : ?>
						<div class="sfpb-link-preview-cta">
							<span class="sfpb-cta-btn"><?php echo esc_html( $button_label ); ?></span>
						</div>
					<?php endif; ?>
				</a>
			</div>
		<?php endif; ?>

		<!-- CARD FOOTER (ENGAGEMENT & ACTIONS) -->
		<footer class="sfpb-card__footer">
			<!-- Counters Row -->
			<div class="sfpb-card__counters">
				<?php if ( $fake_likes > 0 || 'instagram' === $platform || 'tiktok' === $platform ) : ?>
					<span class="sfpb-counter sfpb-counter--likes" title="<?php esc_attr_e( 'Likes', 'social-feed-preview-builder' ); ?>">
						<?php echo $like_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="sfpb-counter-val"><?php echo esc_html( sfpb_format_number( $fake_likes ) ); ?></span>
						<span class="sfpb-sr-only"><?php esc_html_e( 'likes', 'social-feed-preview-builder' ); ?></span>
					</span>
				<?php endif; ?>

				<?php if ( $fake_comments > 0 ) : ?>
					<span class="sfpb-counter sfpb-counter--comments" title="<?php esc_attr_e( 'Comments', 'social-feed-preview-builder' ); ?>">
						<?php echo $comment_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="sfpb-counter-val"><?php echo esc_html( sfpb_format_number( $fake_comments ) ); ?></span>
						<span class="sfpb-sr-only"><?php esc_html_e( 'comments', 'social-feed-preview-builder' ); ?></span>
					</span>
				<?php endif; ?>

				<?php if ( $fake_shares > 0 ) : ?>
					<span class="sfpb-counter sfpb-counter--shares" title="<?php echo ( 'x' === $platform ) ? esc_attr__( 'Retweets', 'social-feed-preview-builder' ) : esc_attr__( 'Shares', 'social-feed-preview-builder' ); ?>">
						<?php echo ( 'x' === $platform ) ? $retweet_icon : $share_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span class="sfpb-counter-val"><?php echo esc_html( sfpb_format_number( $fake_shares ) ); ?></span>
						<span class="sfpb-sr-only"><?php echo ( 'x' === $platform ) ? esc_html__( 'retweets', 'social-feed-preview-builder' ) : esc_html__( 'shares', 'social-feed-preview-builder' ); ?></span>
					</span>
				<?php endif; ?>
			</div>

			<!-- Mock Action Buttons (pure visual proof) -->
			<div class="sfpb-card__actions">
				<button type="button" class="sfpb-action-btn sfpb-action-btn--like" aria-label="<?php esc_attr_e( 'Like this mockup post', 'social-feed-preview-builder' ); ?>">
					<?php echo $like_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="sfpb-action-btn-label"><?php esc_html_e( 'Like', 'social-feed-preview-builder' ); ?></span>
				</button>
				<button type="button" class="sfpb-action-btn sfpb-action-btn--comment" aria-label="<?php esc_attr_e( 'Comment on this mockup post', 'social-feed-preview-builder' ); ?>">
					<?php echo $comment_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="sfpb-action-btn-label"><?php esc_html_e( 'Comment', 'social-feed-preview-builder' ); ?></span>
				</button>
				<button type="button" class="sfpb-action-btn sfpb-action-btn--share" aria-label="<?php esc_attr_e( 'Share this mockup post', 'social-feed-preview-builder' ); ?>">
					<?php echo $share_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="sfpb-action-btn-label"><?php esc_html_e( 'Share', 'social-feed-preview-builder' ); ?></span>
				</button>
			</div>
		</footer>

	</div>
</article>
