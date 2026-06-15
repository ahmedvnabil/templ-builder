/**
 * Admin JavaScript for Social Feed Preview Builder.
 */

document.addEventListener('DOMContentLoaded', function() {
	// 1. Conditional Fields Visibility
	const contentTypeSelect = document.getElementById('sfpb_content_type');
	if (contentTypeSelect) {
		const updateConditionalFields = () => {
			const selectedType = contentTypeSelect.value;
			const conditionals = document.querySelectorAll('.sfpb-conditional');

			conditionals.forEach(el => {
				const dependency = el.getAttribute('data-dependency');
				if (dependency) {
					const parts = dependency.split(':');
					const depField = parts[0];
					const depValue = parts[1];

					if (depField === 'content_type') {
						if (depValue === selectedType) {
							el.classList.add('sfpb-visible');
						} else {
							el.classList.remove('sfpb-visible');
						}
					}
				}
			});
		};

		contentTypeSelect.addEventListener('change', updateConditionalFields);
		updateConditionalFields(); // Run once initially
	}

	// 2. Platform Help Notes
	const platformSelect = document.getElementById('sfpb_platform');
	const platformNote = document.getElementById('sfpb_platform_note');
	if (platformSelect && platformNote) {
		const notes = {
			generic: 'Generic Card: highly adaptable standard layout that blends seamlessly with most web designs.',
			facebook: 'Facebook Inspired: standard card layout with header avatar, post content, large media box, and standard engagement row.',
			instagram: 'Instagram Inspired: media-centric square format, caption below media, and prominent hearts/comments details.',
			x: 'X / Twitter Inspired: rounded avatar aligned left, name and handle inline, with a modern clean layout and thread-like style.',
			linkedin: 'LinkedIn Inspired: professional layout featuring a business-style header, long text formatting, and detailed link previews.',
			tiktok: 'TikTok Inspired: mobile-first portrait aspect ratio card with floating interaction overlay and dark aesthetics.',
			youtube: 'YouTube Inspired: video thumbnail dominant style with post title and author metadata positioned underneath.'
		};

		const updatePlatformNote = () => {
			const platform = platformSelect.value;
			if (notes[platform]) {
				platformNote.textContent = notes[platform];
				platformNote.classList.add('sfpb-visible');
			} else {
				platformNote.classList.remove('sfpb-visible');
			}
		};

		platformSelect.addEventListener('change', updatePlatformNote);
		updatePlatformNote(); // Run once initially
	}

	// 3. WordPress Media Library Uploader
	const uploadButtons = document.querySelectorAll('.sfpb-upload-button');
	uploadButtons.forEach(button => {
		button.addEventListener('click', function(e) {
			e.preventDefault();

			const targetId = this.getAttribute('data-target');
			const targetInput = document.getElementById(targetId);
			if (!targetInput) return;

			// Verify wp.media is available
			if (typeof wp === 'undefined' || !wp.media) {
				alert('WordPress Media Library not loaded.');
				return;
			}

			// Determine media type filter
			let mimeType = '';
			if (targetId.indexOf('video') !== -1) {
				mimeType = 'video';
			} else {
				mimeType = 'image';
			}

			// Create media frame
			const frame = wp.media({
				title: 'Select Media',
				button: {
					text: 'Use this file'
				},
				multiple: false,
				library: {
					type: mimeType
				}
			});

			// On select
			frame.on('select', function() {
				const attachment = frame.state().get('selection').first().toJSON();
				targetInput.value = attachment.url;
				
				// Trigger input change event (useful if other elements monitor this field)
				targetInput.dispatchEvent(new Event('change'));
			});

			// Open frame
			frame.open();
		});
	});

	// 4. Copy Shortcode Buttons in Tools Page
	const copyButtons = document.querySelectorAll('.sfpb-copy-btn');
	copyButtons.forEach(btn => {
		btn.addEventListener('click', function() {
			const textToCopy = this.getAttribute('data-code');
			if (!textToCopy) return;

			navigator.clipboard.writeText(textToCopy).then(() => {
				const originalText = this.textContent;
				this.textContent = 'Copied!';
				this.style.background = '#e6ffed';
				this.style.color = '#22863a';
				this.style.borderColor = '#34d058';

				setTimeout(() => {
					this.textContent = originalText;
					this.style.background = '';
					this.style.color = '';
					this.style.borderColor = '';
				}, 2000);
			}).catch(err => {
				console.error('Failed to copy text: ', err);
			});
		});
	});

	// 5. Live Shortcode Builder Generator
	const builderInputs = document.querySelectorAll('.sfpb-builder-input');
	const builderOutput = document.getElementById('sfpb_builder_output');
	const builderCopyBtn = document.getElementById('sfpb_builder_copy_btn');

	if (builderInputs.length && builderOutput && builderCopyBtn) {
		const updateBuilderShortcode = () => {
			const platformSelectVal = document.getElementById('sfpb_builder_platform').value;
			const contentTypeSelectVal = document.getElementById('sfpb_builder_content_type').value;
			const campaignInputVal = document.getElementById('sfpb_builder_campaign').value.trim();
			const limitInputVal = document.getElementById('sfpb_builder_limit').value;
			const columnsInputVal = document.getElementById('sfpb_builder_columns').value;

			let shortcode = '[social_feed_preview';

			if (platformSelectVal) {
				shortcode += ` platform="${platformSelectVal}"`;
			}
			if (contentTypeSelectVal) {
				shortcode += ` content_type="${contentTypeSelectVal}"`;
			}
			if (campaignInputVal) {
				shortcode += ` campaign="${campaignInputVal}"`;
			}
			if (limitInputVal) {
				shortcode += ` limit="${limitInputVal}"`;
			}
			if (columnsInputVal) {
				shortcode += ` columns="${columnsInputVal}"`;
			}

			shortcode += ']';

			builderOutput.textContent = shortcode;
			builderCopyBtn.setAttribute('data-code', shortcode);
		};

		builderInputs.forEach(input => {
			input.addEventListener('input', updateBuilderShortcode);
			input.addEventListener('change', updateBuilderShortcode);
		});

		updateBuilderShortcode();
	}

	// 6. Copyable Shortcode Inline Elements (with visual feedback)
	document.addEventListener('click', function(e) {
		const target = e.target.closest('.sfpb-copyable-shortcode');
		if (!target) return;

		e.preventDefault();
		const textToCopy = target.textContent || target.getAttribute('data-code');
		if (!textToCopy) return;

		navigator.clipboard.writeText(textToCopy).then(() => {
			const originalText = target.textContent;
			const originalBg = target.style.background || target.style.backgroundColor;
			const originalColor = target.style.color;

			target.textContent = 'Copied!';
			target.style.background = '#e6ffed';
			target.style.color = '#22863a';
			target.style.borderColor = '#34d058';

			setTimeout(() => {
				target.textContent = originalText;
				target.style.background = originalBg;
				target.style.color = originalColor;
				target.style.borderColor = '';
			}, 1500);
		}).catch(err => {
			console.error('Failed to copy text: ', err);
		});
	});
});
