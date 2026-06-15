/**
 * Optional Frontend JavaScript for Templ Builder.
 * Provides progressive enhancements (like Accordion FAQ actions).
 */

document.addEventListener('DOMContentLoaded', function() {
	// 1. Accordion FAQ Interactions
	const faqs = document.querySelectorAll('.tb-faq');
	faqs.forEach(faq => {
		const question = faq.querySelector('.tb-faq__question');
		const answer = faq.querySelector('.tb-faq__answer');

		if (!question || !answer) return;

		question.addEventListener('click', function(e) {
			e.preventDefault();
			const isOpen = faq.classList.contains('open');

			// Close all other FAQs in the same container for a true accordion feel
			const parentWrap = faq.closest('.tb-wrap');
			if (parentWrap) {
				const siblingFaqs = parentWrap.querySelectorAll('.tb-faq');
				siblingFaqs.forEach(sibling => {
					if (sibling !== faq) {
						sibling.classList.remove('open');
						const siblingAns = sibling.querySelector('.tb-faq__answer');
						if (siblingAns) {
							siblingAns.style.maxHeight = '0';
						}
					}
				});
			}

			// Toggle target
			if (isOpen) {
				faq.classList.remove('open');
				answer.style.maxHeight = '0';
			} else {
				faq.classList.add('open');
				answer.style.maxHeight = answer.scrollHeight + 'px';
			}
		});
	});

	// 2. Play overlay for mock video containers
	const videos = document.querySelectorAll('.tb-card__media video');
	videos.forEach(video => {
		video.addEventListener('play', function() {
			const container = this.closest('.tb-card__media');
			if (container) {
				container.classList.add('tb-video-playing');
			}
		});
		video.addEventListener('pause', function() {
			const container = this.closest('.tb-card__media');
			if (container) {
				container.classList.remove('tb-video-playing');
			}
		});
	});
});
