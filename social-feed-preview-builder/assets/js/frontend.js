/**
 * Optional Frontend JavaScript for Social Feed Preview Builder.
 * Minimalist interaction logic that does not break the layout if disabled.
 */

document.addEventListener('DOMContentLoaded', function() {
	// 1. Mock Video Interaction
	const videoContainers = document.querySelectorAll('.sfpb-video-wrapper');
	videoContainers.forEach(container => {
		const video = container.querySelector('video');
		const overlay = container.querySelector('.sfpb-video-overlay');
		
		if (!video || !overlay) return;

		// Toggle play/pause on clicking container
		container.addEventListener('click', function() {
			if (video.paused) {
				// Pause all other videos first to be polite
				document.querySelectorAll('video').forEach(v => v.pause());
				
				video.play();
				overlay.style.opacity = '0';
			} else {
				video.pause();
				overlay.style.opacity = '1';
			}
		});

		// Monitor video events to sync overlays (e.g. if native controls used)
		video.addEventListener('play', () => {
			overlay.style.opacity = '0';
		});
		video.addEventListener('pause', () => {
			overlay.style.opacity = '1';
		});
	});

	// 2. Mock Likes Button Click Interactivity
	const cards = document.querySelectorAll('.sfpb-card');
	cards.forEach(card => {
		const likeBtn = card.querySelector('.sfpb-action-btn--like');
		const likesValEl = card.querySelector('.sfpb-counter--likes .sfpb-counter-val');

		if (!likeBtn) return;

		likeBtn.addEventListener('click', function(e) {
			e.preventDefault();
			e.stopPropagation();

			const isActive = this.classList.toggle('sfpb-active');
			
			// Change icon styling
			if (isActive) {
				this.style.color = 'var(--sfpb-color-tiktok)';
				if (likesValEl) {
					// Increment likes mockup
					let count = parseInt(likesValEl.textContent.replace('K', '').replace('M', ''));
					if (!isNaN(count)) {
						likesValEl.textContent = (count + 1) + (likesValEl.textContent.indexOf('K') !== -1 ? 'K' : (likesValEl.textContent.indexOf('M') !== -1 ? 'M' : ''));
					}
				}
			} else {
				this.style.color = '';
				if (likesValEl) {
					// Decrement likes mockup
					let count = parseInt(likesValEl.textContent.replace('K', '').replace('M', ''));
					if (!isNaN(count)) {
						likesValEl.textContent = (count - 1) + (likesValEl.textContent.indexOf('K') !== -1 ? 'K' : (likesValEl.textContent.indexOf('M') !== -1 ? 'M' : ''));
					}
				}
			}
		});

		// 3. Mock Share Button
		const shareBtn = card.querySelector('.sfpb-action-btn--share');
		if (shareBtn) {
			shareBtn.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				
				// Create a quick temporary notification bubble
				const toast = document.createElement('div');
				toast.textContent = 'Mock Sharing Triggered!';
				toast.style.position = 'fixed';
				toast.style.bottom = '20px';
				toast.style.left = '50%';
				toast.style.transform = 'translateX(-50%)';
				toast.style.background = '#2c3e50';
				toast.style.color = '#fff';
				toast.style.padding = '10px 20px';
				toast.style.borderRadius = '30px';
				toast.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';
				toast.style.zIndex = '99999';
				toast.style.fontSize = '13px';
				toast.style.fontFamily = 'sans-serif';
				toast.style.transition = 'opacity 0.3s';
				
				document.body.appendChild(toast);
				
				setTimeout(() => {
					toast.style.opacity = '0';
					setTimeout(() => toast.remove(), 300);
				}, 1500);
			});
		}
	});
});
