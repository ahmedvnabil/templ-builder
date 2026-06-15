/**
 * Admin JavaScript for Templ Builder.
 */

document.addEventListener('DOMContentLoaded', function() {
	// 1. Meta Box Tabs Switching
	const tabButtons = document.querySelectorAll('.tb-meta-tab-btn');
	const tabContents = document.querySelectorAll('.tb-meta-tab-content');

	if (tabButtons.length && tabContents.length) {
		tabButtons.forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				const targetTab = this.getAttribute('data-tab');

				// Reset active states
				tabButtons.forEach(b => b.classList.remove('active'));
				tabContents.forEach(c => c.classList.remove('active'));

				// Set active target
				this.classList.add('active');
				const targetEl = document.getElementById('tb-tab-' + targetTab);
				if (targetEl) {
					targetEl.classList.add('active');
				}
				
				// Set hidden field to preserve tab on save/reload if needed
				const activeTabInput = document.getElementById('tb_active_meta_tab');
				if (activeTabInput) {
					activeTabInput.value = targetTab;
				}
			});
		});
	}

	// 2. Structured JSON Validation & Formatting
	const jsonTextarea = document.getElementById('tb_custom_json_fields');
	const validateBtn = document.getElementById('tb_validate_json_btn');
	const formatBtn = document.getElementById('tb_format_json_btn');
	const jsonBadge = document.getElementById('tb_json_validation_badge');

	if (jsonTextarea && jsonBadge) {
		const updateBadge = (status, msg = '') => {
			jsonBadge.className = 'tb-validation-badge';
			if ('valid' === status) {
				jsonBadge.classList.add('tb-validation-badge--valid');
				jsonBadge.textContent = 'JSON Structure is Valid!';
				jsonBadge.style.display = 'inline-block';
			} else if ('invalid' === status) {
				jsonBadge.classList.add('tb-validation-badge--invalid');
				jsonBadge.textContent = 'Error: ' + msg;
				jsonBadge.style.display = 'inline-block';
			} else {
				jsonBadge.style.display = 'none';
			}
		};

		// Helper validator
		const validateJSON = (silent = false) => {
			const val = jsonTextarea.value.trim();
			if (!val) {
				updateBadge('empty');
				return true;
			}
			try {
				JSON.parse(val);
				if (!silent) updateBadge('valid');
				return true;
			} catch (e) {
				if (!silent) updateBadge('invalid', e.message);
				return false;
			}
		};

		// Validate on input or click
		if (validateBtn) {
			validateBtn.addEventListener('click', function(e) {
				e.preventDefault();
				validateJSON(false);
			});
		}

		// Format JSON button
		if (formatBtn) {
			formatBtn.addEventListener('click', function(e) {
				e.preventDefault();
				const val = jsonTextarea.value.trim();
				if (!val) return;
				try {
					const parsed = JSON.parse(val);
					jsonTextarea.value = JSON.stringify(parsed, null, 4);
					updateBadge('valid');
				} catch (err) {
					updateBadge('invalid', err.message);
				}
			});
		}

		// Run initially if value exists
		if (jsonTextarea.value.trim()) {
			validateJSON(true);
		}
	}

	// 3. WordPress Media Library integration
	const uploadButtons = document.querySelectorAll('.tb-upload-button');
	uploadButtons.forEach(button => {
		button.addEventListener('click', function(e) {
			e.preventDefault();

			const targetId = this.getAttribute('data-target');
			const targetInput = document.getElementById(targetId);
			const altInput = document.getElementById(targetId + '_alt');
			if (!targetInput) return;

			if (typeof wp === 'undefined' || !wp.media) {
				alert('WordPress Media Library is not enqueued.');
				return;
			}

			// Create frame
			const frame = wp.media({
				title: 'Select Media File',
				button: {
					text: 'Use this media'
				},
				multiple: false,
				library: {
					type: 'image'
				}
			});

			// On select
			frame.on('select', function() {
				const attachment = frame.state().get('selection').first().toJSON();
				targetInput.value = attachment.url;
				targetInput.dispatchEvent(new Event('change'));

				if (altInput && attachment.alt) {
					altInput.value = attachment.alt;
				}
			});

			frame.open();
		});
	});

	// 4. Live Shortcode Builder Generator
	const builderInputs = document.querySelectorAll('.tb-builder-input');
	const builderOutput = document.getElementById('tb_builder_output');
	const builderCopyBtn = document.getElementById('tb_builder_copy_btn');

	if (builderInputs.length && builderOutput && builderCopyBtn) {
		const updateShortcodeString = () => {
			const typeSelect = document.getElementById('tb_builder_type').value;
			const collectionSelect = document.getElementById('tb_builder_collection').value;
			const templateSelect = document.getElementById('tb_builder_template').value;
			const limitInput = document.getElementById('tb_builder_limit').value;
			const columnsInput = document.getElementById('tb_builder_columns').value;
			const statusSelect = document.getElementById('tb_builder_status').value;
			const featuredCheck = document.getElementById('tb_builder_featured').checked;
			const orderbySelect = document.getElementById('tb_builder_orderby').value;
			const orderSelect = document.getElementById('tb_builder_order').value;
			const cssInput = document.getElementById('tb_builder_class').value.trim();

			let shortcode = '[templ';

			if (typeSelect) {
				shortcode += ` type="${typeSelect}"`;
			}
			if (collectionSelect) {
				shortcode += ` collection="${collectionSelect}"`;
			}
			if (templateSelect) {
				shortcode += ` template="${templateSelect}"`;
			}
			if (limitInput) {
				shortcode += ` limit="${limitInput}"`;
			}
			if (columnsInput) {
				shortcode += ` columns="${columnsInput}"`;
			}
			if (statusSelect) {
				shortcode += ` status="${statusSelect}"`;
			}
			if (featuredCheck) {
				shortcode += ` featured="1"`;
			}
			if (orderbySelect) {
				shortcode += ` orderby="${orderbySelect}"`;
			}
			if (orderSelect) {
				shortcode += ` order="${orderSelect}"`;
			}
			if (cssInput) {
				shortcode += ` class="${cssInput}"`;
			}

			shortcode += ']';

			builderOutput.textContent = shortcode;
			builderCopyBtn.setAttribute('data-code', shortcode);
		};

		builderInputs.forEach(input => {
			input.addEventListener('input', updateShortcodeString);
			input.addEventListener('change', updateShortcodeString);
		});

		updateShortcodeString(); // Initial run
	}

	// 5. General Copying to Clipboard Handler (Non-Blocking Visual Feedback)
	document.addEventListener('click', function(e) {
		const btn = e.target.closest('.tb-copy-btn, .tb-copyable-shortcode');
		if (!btn) return;

		e.preventDefault();
		const textToCopy = btn.getAttribute('data-code') || btn.textContent;
		if (!textToCopy) return;

		navigator.clipboard.writeText(textToCopy).then(() => {
			const originalText = btn.textContent;
			const originalBg = btn.style.background || btn.style.backgroundColor;
			const originalColor = btn.style.color;

			btn.textContent = 'Copied!';
			btn.style.background = '#dcfce7';
			btn.style.color = '#15803d';
			btn.style.borderColor = '#bbf7d0';

			setTimeout(() => {
				btn.textContent = originalText;
				btn.style.background = originalBg;
				btn.style.color = originalColor;
				btn.style.borderColor = '';
			}, 1500);
		}).catch(err => {
			console.error('Copy failure: ', err);
		});
	});
});
