(function () {
	'use strict';

	const navbar = document.querySelector('.navbar');
	const toggle = document.querySelector('.navbar__toggle');
	const nav = document.querySelector('.navbar__nav');

	if (toggle && nav) {
		toggle.addEventListener('click', () => {
			const isOpen = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', String(isOpen));
		});

		nav.querySelectorAll('a').forEach((link) => {
			link.addEventListener('click', () => {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
			});
		});
	}

	if (navbar) {
		window.addEventListener('scroll', () => {
			navbar.classList.toggle('is-scrolled', window.scrollY > 10);
		}, { passive: true });
	}

	document.querySelectorAll('[data-carousel]').forEach((carousel) => {
		if (carousel.hasAttribute('data-carousel-cards')) {
			initCardsCarousel(carousel);
			return;
		}

		initStandardCarousel(carousel);
	});

	function initCardsCarousel(carousel) {
		const track = carousel.querySelector('.about-cards-carousel__track');
		const items = Array.from(carousel.querySelectorAll('[data-carousel-item]'));

		if (!track || !items.length) {
			return;
		}

		const prevBtn = carousel.querySelector('[data-carousel-prev]');
		const nextBtn = carousel.querySelector('[data-carousel-next]');
		const dotsContainer = carousel.querySelector('[data-carousel-dots]');
		const configuredVisible = parseInt(carousel.getAttribute('data-carousel-visible'), 10) || 3;
		const carouselLabel = carousel.getAttribute('data-carousel-label') || 'Carrusel';
		let current = 0;
		let autoplayTimer;
		let dots = [];

		function getVisibleCount() {
			if (window.matchMedia('(max-width: 640px)').matches) {
				return 1;
			}

			if (window.matchMedia('(max-width: 992px)').matches) {
				return Math.min(2, configuredVisible);
			}

			return configuredVisible;
		}

		function getMaxIndex() {
			return Math.max(0, items.length - getVisibleCount());
		}

		function updatePosition() {
			const visible = getVisibleCount();
			const maxIndex = getMaxIndex();

			if (current > maxIndex) {
				current = maxIndex;
			}

			const gap = parseFloat(getComputedStyle(track).gap) || 0;
			const itemWidth = items[0].getBoundingClientRect().width;
			track.style.transform = 'translate3d(-' + (current * (itemWidth + gap)) + 'px, 0, 0)';

			items.forEach((item, index) => {
				const inView = index >= current && index < current + visible;
				item.setAttribute('aria-hidden', inView ? 'false' : 'true');
			});

			dots.forEach((dot, index) => {
				dot.classList.toggle('is-active', index === current);
			});
		}

		function rebuildDots() {
			if (!dotsContainer) {
				return;
			}

			dotsContainer.innerHTML = '';
			dots = [];

			const maxIndex = getMaxIndex();
			if (maxIndex === 0) {
				return;
			}

			for (let i = 0; i <= maxIndex; i += 1) {
				const dot = document.createElement('button');
				dot.type = 'button';
				dot.className = 'carousel__dot' + (i === current ? ' is-active' : '');
				dot.setAttribute('aria-label', carouselLabel + ' ' + (i + 1));
				dot.addEventListener('click', () => {
					goTo(i);
					resetAutoplay();
				});
				dotsContainer.appendChild(dot);
				dots.push(dot);
			}
		}

		function goTo(index) {
			const maxIndex = getMaxIndex();
			if (maxIndex === 0) {
				return;
			}

			current = ((index % (maxIndex + 1)) + (maxIndex + 1)) % (maxIndex + 1);
			updatePosition();
		}

		function startAutoplay() {
			clearInterval(autoplayTimer);

			if (getMaxIndex() === 0) {
				return;
			}

			autoplayTimer = setInterval(() => {
				const maxIndex = getMaxIndex();
				goTo(current >= maxIndex ? 0 : current + 1);
			}, 6000);
		}

		function resetAutoplay() {
			clearInterval(autoplayTimer);
			startAutoplay();
		}

		if (prevBtn && nextBtn) {
			prevBtn.addEventListener('click', () => {
				goTo(current - 1);
				resetAutoplay();
			});
			nextBtn.addEventListener('click', () => {
				goTo(current + 1);
				resetAutoplay();
			});
		}

		window.addEventListener('resize', () => {
			rebuildDots();
			updatePosition();
		});

		rebuildDots();
		updatePosition();
		startAutoplay();
	}

	function initStandardCarousel(carousel) {
		const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
		if (!slides.length) {
			return;
		}

		const prevBtn = carousel.querySelector('[data-carousel-prev]');
		const nextBtn = carousel.querySelector('[data-carousel-next]');
		const dotsContainer = carousel.querySelector('[data-carousel-dots]');
		const isVideoCarousel = carousel.hasAttribute('data-carousel-video');
		const carouselLabel = carousel.getAttribute('data-carousel-label') || 'Carrusel';
		let current = 0;
		let autoplayTimer;

		function pauseSlideVideos(slide) {
			slide.querySelectorAll('iframe[data-src]').forEach((iframe) => {
				iframe.src = '';
			});
		}

		function playSlideVideos(slide) {
			slide.querySelectorAll('iframe[data-src]').forEach((iframe) => {
				if (!iframe.src) {
					iframe.src = iframe.dataset.src;
				}
			});
		}

		if (dotsContainer) {
			slides.forEach((_, index) => {
				const dot = document.createElement('button');
				dot.type = 'button';
				dot.className = 'carousel__dot' + (index === 0 ? ' is-active' : '');
				dot.setAttribute('aria-label', carouselLabel + ' ' + (index + 1));
				dot.addEventListener('click', () => goTo(index));
				dotsContainer.appendChild(dot);
			});
		}

		const dots = dotsContainer ? Array.from(dotsContainer.querySelectorAll('.carousel__dot')) : [];

		function goTo(index) {
			pauseSlideVideos(slides[current]);
			slides[current].classList.remove('is-active');
			slides[current].setAttribute('aria-hidden', 'true');
			if (dots[current]) {
				dots[current].classList.remove('is-active');
			}

			current = (index + slides.length) % slides.length;

			slides[current].classList.add('is-active');
			slides[current].setAttribute('aria-hidden', 'false');
			if (dots[current]) {
				dots[current].classList.add('is-active');
			}

			if (isVideoCarousel) {
				playSlideVideos(slides[current]);
			}
		}

		function startAutoplay() {
			if (isVideoCarousel || slides.length < 2) {
				return;
			}

			autoplayTimer = setInterval(() => goTo(current + 1), 6000);
		}

		function resetAutoplay() {
			clearInterval(autoplayTimer);
			startAutoplay();
		}

		if (prevBtn && nextBtn) {
			prevBtn.addEventListener('click', () => { goTo(current - 1); resetAutoplay(); });
			nextBtn.addEventListener('click', () => { goTo(current + 1); resetAutoplay(); });
		}

		slides[0].classList.add('is-active');
		if (isVideoCarousel) {
			playSlideVideos(slides[0]);
		}
		startAutoplay();
	}

	document.querySelectorAll('.faq__question').forEach((button) => {
		button.addEventListener('click', () => {
			const expanded = button.getAttribute('aria-expanded') === 'true';
			const answer = document.getElementById(button.getAttribute('aria-controls'));

			document.querySelectorAll('.faq__question').forEach((other) => {
				if (other !== button) {
					other.setAttribute('aria-expanded', 'false');
					const otherAnswer = document.getElementById(other.getAttribute('aria-controls'));
					if (otherAnswer) otherAnswer.hidden = true;
				}
			});

			button.setAttribute('aria-expanded', String(!expanded));
			if (answer) answer.hidden = expanded;
		});
	});

	const form = document.getElementById('contact-form');
	const feedback = document.getElementById('contact-feedback');

	document.querySelectorAll('[data-contact-service]').forEach((button) => {
		button.addEventListener('click', (event) => {
			event.preventDefault();

			const serviceName = button.getAttribute('data-contact-service');
			const contactSection = document.getElementById('contacto');
			const serviceField = document.getElementById('contact-service');
			const nameField = document.getElementById('contact-name');

			if (serviceField && serviceName) {
				serviceField.value = serviceName;
			}

			if (contactSection) {
				contactSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}

			window.setTimeout(() => {
				if (form) {
					form.classList.add('contact-form--prefilled');
					window.setTimeout(() => form.classList.remove('contact-form--prefilled'), 1800);
				}

				if (serviceField && serviceName) {
					serviceField.focus({ preventScroll: true });
				} else if (nameField) {
					nameField.focus({ preventScroll: true });
				}
			}, 450);
		});
	});

	if (form && typeof sercotecLanding !== 'undefined') {
		const i18n = sercotecLanding.i18n || {};
		const turnstileEnabled = Boolean(sercotecLanding.turnstileSiteKey);
		const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		const phonePattern = /^[+0-9()\-\s]{6,30}$/;

		function getTurnstileResponse() {
			return form.querySelector('[name="cf-turnstile-response"]')?.value.trim() || '';
		}

		function resetTurnstile() {
			const widget = form.querySelector('.contact-form__turnstile');

			if (window.turnstile && widget) {
				window.turnstile.reset(widget);
			}
		}

		function setCaptchaError(message) {
			const error = form.querySelector('#contact-captcha-error');
			const widget = form.querySelector('.contact-form__turnstile');

			if (widget) {
				widget.classList.toggle('is-invalid', Boolean(message));
			}

			if (error) {
				error.textContent = message || '';
			}
		}

		function getFieldElements(fieldName) {
			return {
				input: form.querySelector('#contact-' + fieldName),
				error: form.querySelector('#contact-' + fieldName + '-error'),
			};
		}

		function clearFieldError(fieldName) {
			const { input, error } = getFieldElements(fieldName);

			if (input) {
				input.classList.remove('is-invalid');
				input.removeAttribute('aria-invalid');
			}

			if (error) {
				error.textContent = '';
			}
		}

		function setFieldError(fieldName, message) {
			if (fieldName === 'captcha') {
				setCaptchaError(message);
				return;
			}

			const { input, error } = getFieldElements(fieldName);

			if (input) {
				input.classList.add('is-invalid');
				input.setAttribute('aria-invalid', 'true');
			}

			if (error) {
				error.textContent = message;
			}
		}

		function clearAllFieldErrors() {
			['name', 'email', 'phone', 'service', 'message'].forEach(clearFieldError);
			setCaptchaError('');
		}

		function getAllowedServices() {
			const serviceField = form.querySelector('#contact-service');

			if (!serviceField) {
				return [];
			}

			return Array.from(serviceField.options)
				.map((option) => option.value)
				.filter(Boolean);
		}

		function validateContactForm() {
			const errors = {};
			const name = form.querySelector('#contact-name')?.value.trim() || '';
			const email = form.querySelector('#contact-email')?.value.trim() || '';
			const phone = form.querySelector('#contact-phone')?.value.trim() || '';
			const service = form.querySelector('#contact-service')?.value.trim() || '';
			const message = form.querySelector('#contact-message')?.value.trim() || '';

			if (!name) {
				errors.name = i18n.nameRequired || 'Ingresa tu nombre completo.';
			} else if (name.length > 120) {
				errors.name = i18n.nameMax || 'El nombre no puede superar 120 caracteres.';
			}

			if (!email) {
				errors.email = i18n.emailRequired || 'Ingresa tu email.';
			} else if (!emailPattern.test(email)) {
				errors.email = i18n.emailInvalid || 'Ingresa un email válido.';
			}

			if (phone) {
				if (phone.length > 30) {
					errors.phone = i18n.phoneMax || 'El teléfono no puede superar 30 caracteres.';
				} else if (!phonePattern.test(phone)) {
					errors.phone = i18n.phoneInvalid || 'Ingresa un teléfono válido.';
				}
			}

			if (service) {
				const allowedServices = getAllowedServices();

				if (!allowedServices.includes(service)) {
					errors.service = i18n.serviceInvalid || 'Selecciona un servicio válido.';
				}
			}

			if (!message) {
				errors.message = i18n.messageRequired || 'Ingresa un mensaje.';
			} else if (message.length < 10) {
				errors.message = i18n.messageMin || 'El mensaje debe tener al menos 10 caracteres.';
			} else if (message.length > 2000) {
				errors.message = i18n.messageMax || 'El mensaje no puede superar 2000 caracteres.';
			}

			if (turnstileEnabled && !getTurnstileResponse()) {
				errors.captcha = i18n.captchaRequired || 'Completa la verificación de seguridad.';
			}

			return errors;
		}

		function showValidationErrors(errors) {
			clearAllFieldErrors();

			Object.entries(errors).forEach(([fieldName, message]) => {
				setFieldError(fieldName, message);
			});

			const firstInvalid = form.querySelector('.is-invalid');

			if (firstInvalid) {
				firstInvalid.focus({ preventScroll: false });
			}
		}

		form.querySelectorAll('input, textarea, select').forEach((field) => {
			field.addEventListener('input', () => {
				const fieldName = field.id.replace('contact-', '');
				clearFieldError(fieldName);
			});

			field.addEventListener('change', () => {
				const fieldName = field.id.replace('contact-', '');
				clearFieldError(fieldName);
			});
		});

		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			feedback.textContent = '';
			feedback.className = 'contact-form__feedback';

			const validationErrors = validateContactForm();

			if (Object.keys(validationErrors).length > 0) {
				showValidationErrors(validationErrors);
				feedback.textContent = Object.values(validationErrors)[0];
				feedback.classList.add('is-error');
				return;
			}

			clearAllFieldErrors();

			const submitBtn = form.querySelector('.contact-form__submit');
			const submitLabel = i18n.submitLabel || 'Enviar mensaje';
			const submittingLabel = i18n.submittingLabel || 'Enviando...';

			submitBtn.disabled = true;
			submitBtn.textContent = submittingLabel;

			const formData = new FormData(form);
			formData.append('action', 'sercotec_contact');
			formData.append('nonce', sercotecLanding.nonce);

			try {
				const response = await fetch(sercotecLanding.ajaxUrl, {
					method: 'POST',
					body: formData,
				});

				const result = await response.json();

				if (result.success) {
					feedback.textContent = result.data.message;
					feedback.classList.add('is-success');
					form.reset();
					clearAllFieldErrors();
					resetTurnstile();
				} else {
					feedback.textContent = result.data?.message || 'Error al enviar el formulario.';
					feedback.classList.add('is-error');
					resetTurnstile();
				}
			} catch {
				feedback.textContent = 'Error de conexión. Intenta nuevamente.';
				feedback.classList.add('is-error');
				resetTurnstile();
			} finally {
				submitBtn.disabled = false;
				submitBtn.textContent = submitLabel;
			}
		});
	}
})();

(function ($) {
	'use strict';

	if (typeof $ === 'undefined' || typeof wp === 'undefined' || !wp.media) {
		return;
	}

	function initImageMediaField(options) {
		const $select = $(options.selectSelector);
		const $remove = $(options.removeSelector);
		const $input = $(options.inputSelector);
		const $preview = $(options.previewSelector);

		if (!$select.length) {
			return;
		}

		let mediaFrame;

		function renderPreview(url) {
			if (url) {
				$preview.html('<img src="' + url + '" alt="">');
				$remove.prop('disabled', false);
			} else {
				$preview.html('<span class="sercotec-service-image-field__placeholder">' + options.emptyLabel + '</span>');
				$remove.prop('disabled', true);
			}
		}

		$select.on('click', function (event) {
			event.preventDefault();

			if (mediaFrame) {
				mediaFrame.open();
				return;
			}

			mediaFrame = wp.media({
				title: options.mediaTitle,
				button: { text: options.mediaButton },
				library: { type: 'image' },
				multiple: false,
			});

			mediaFrame.on('select', function () {
				const attachment = mediaFrame.state().get('selection').first().toJSON();
				$input.val(attachment.id);
				renderPreview(attachment.sizes?.medium?.url || attachment.url);
			});

			mediaFrame.open();
		});

		$remove.on('click', function (event) {
			event.preventDefault();
			$input.val('');
			renderPreview('');
		});
	}

	initImageMediaField({
		selectSelector: '#sercotec-service-image-select',
		removeSelector: '#sercotec-service-image-remove',
		inputSelector: '#sercotec_service_image_id',
		previewSelector: '#sercotec-service-image-preview',
		emptyLabel: 'Sin imagen seleccionada',
		mediaTitle: 'Seleccionar imagen',
		mediaButton: 'Usar esta imagen',
	});

	initImageMediaField({
		selectSelector: '#sercotec-about-card-image-select',
		removeSelector: '#sercotec-about-card-image-remove',
		inputSelector: '#sercotec_about_card_image_id',
		previewSelector: '#sercotec-about-card-image-preview',
		emptyLabel: 'Sin imagen seleccionada',
		mediaTitle: 'Agregar imagen',
		mediaButton: 'Usar esta imagen',
	});

	const footerConfig = window.sercotecFooterAdmin || {};
	const footerLabels = footerConfig.labels || {};
	let brandMediaFrame = null;
	let socialMediaFrame = null;

	function renderBrandPreview(url) {
		const preview = $('#sercotec-footer-logo-preview');

		if (!preview.length) {
			return;
		}

		if (url) {
			preview.html('<img src="' + url + '" alt="">');
			$('#sercotec-footer-logo-remove').prop('disabled', false);
		} else {
			preview.html('<span class="sercotec-service-image-field__placeholder">' + (footerLabels.noIcon || 'Sin logo') + '</span>');
			$('#sercotec-footer-logo-remove').prop('disabled', true);
		}
	}

	function renderSocialPreview($row) {
		const preview = $row.find('.sercotec-social-row__icon-preview');
		const iconId = $row.find('.sercotec-social-row__icon-id').val();
		const iconKey = $row.find('.sercotec-social-row__icon-key').val();
		const $selected = $row.find('.sercotec-social-icon-picker__option.is-selected').first();

		if (iconId) {
			const imgSrc = $row.data('custom-icon-url');

			if (imgSrc) {
				preview.html('<img src="' + imgSrc + '" alt="">');
				$row.find('.sercotec-social-row__icon-remove').prop('disabled', false);
				return;
			}
		}

		if (iconKey && $selected.length) {
			preview.html($selected.html());
			$row.find('.sercotec-social-row__icon-remove').prop('disabled', false);
			return;
		}

		preview.html('<span class="sercotec-social-row__icon-placeholder">' + (footerLabels.noIcon || 'Sin logo') + '</span>');
		$row.find('.sercotec-social-row__icon-remove').prop('disabled', true);
	}

	function setLibrarySelection($row, iconKey) {
		$row.find('.sercotec-social-row__icon-key').val(iconKey || '');
		$row.find('.sercotec-social-row__icon-id').val('');
		$row.removeData('custom-icon-url');
		$row.find('.sercotec-social-icon-picker__option').removeClass('is-selected');

		if (iconKey) {
			$row.find('.sercotec-social-icon-picker__option[data-icon-key="' + iconKey + '"]').addClass('is-selected');
		}

		renderSocialPreview($row);
	}

	function getNextSocialIndex() {
		let maxIndex = -1;

		$('#sercotec-footer-social-list .sercotec-social-row').each(function () {
			const index = parseInt($(this).attr('data-index'), 10);

			if (!Number.isNaN(index) && index > maxIndex) {
				maxIndex = index;
			}
		});

		return maxIndex + 1;
	}

	function createSocialRow(index) {
		const template = footerConfig.rowTemplate || '';

		return $(template.replace(/__index__/g, String(index)));
	}

	function reindexSocialRows() {
		$('#sercotec-footer-social-list .sercotec-social-row').each(function (rowIndex) {
			const $row = $(this);

			$row.attr('data-index', String(rowIndex));
			$row.find('[name^="sercotec_footer_social_links["]').each(function () {
				const $field = $(this);
				const fieldName = $field.attr('name').replace(
					/sercotec_footer_social_links\[\d+]/,
					'sercotec_footer_social_links[' + rowIndex + ']'
				);

				$field.attr('name', fieldName);
			});
		});
	}

	if ($('#sercotec-footer-logo-select').length) {
		$('#sercotec-footer-logo-select').on('click', function (event) {
			event.preventDefault();

			if (brandMediaFrame) {
				brandMediaFrame.open();
				return;
			}

			brandMediaFrame = wp.media({
				title: footerLabels.mediaTitle || 'Seleccionar logo',
				button: { text: footerLabels.mediaButton || 'Usar este logo' },
				library: { type: 'image' },
				multiple: false,
			});

			brandMediaFrame.on('select', function () {
				const attachment = brandMediaFrame.state().get('selection').first().toJSON();
				$('#sercotec_footer_logo_id').val(attachment.id);
				renderBrandPreview(attachment.sizes?.medium?.url || attachment.url);
			});

			brandMediaFrame.open();
		});

		$('#sercotec-footer-logo-remove').on('click', function (event) {
			event.preventDefault();
			$('#sercotec_footer_logo_id').val('');
			renderBrandPreview('');
		});
	}

	if ($('#sercotec-footer-social-add').length) {
		$('#sercotec-footer-social-add').on('click', function (event) {
			event.preventDefault();
			const index = getNextSocialIndex();
			$('#sercotec-footer-social-list').append(createSocialRow(index));
		});

		$('#sercotec-footer-social-list').on('click', '.sercotec-social-row__remove', function (event) {
			event.preventDefault();
			$(this).closest('.sercotec-social-row').remove();
			reindexSocialRows();
		});

		$('#sercotec-footer-social-list').on('click', '.sercotec-social-icon-picker__option', function (event) {
			event.preventDefault();

			const $button = $(this);
			const $row = $button.closest('.sercotec-social-row');
			const iconKey = $button.data('icon-key');

			setLibrarySelection($row, iconKey);
		});

		$('#sercotec-footer-social-list').on('click', '.sercotec-social-row__icon-select', function (event) {
			event.preventDefault();

			const $row = $(this).closest('.sercotec-social-row');

			if (socialMediaFrame) {
				socialMediaFrame.off('select');
			}

			socialMediaFrame = wp.media({
				title: footerLabels.mediaTitle || 'Seleccionar logo de red social',
				button: { text: footerLabels.mediaButton || 'Usar este logo' },
				library: { type: 'image' },
				multiple: false,
			});

			socialMediaFrame.on('select', function () {
				const attachment = socialMediaFrame.state().get('selection').first().toJSON();
				const previewUrl = attachment.sizes?.thumbnail?.url || attachment.url;

				$row.find('.sercotec-social-row__icon-id').val(attachment.id);
				$row.find('.sercotec-social-row__icon-key').val('');
				$row.data('custom-icon-url', previewUrl);
				$row.find('.sercotec-social-icon-picker__option').removeClass('is-selected');
				$row.find('.sercotec-social-row__icon-preview').html('<img src="' + previewUrl + '" alt="">');
				$row.find('.sercotec-social-row__icon-remove').prop('disabled', false);
			});

			socialMediaFrame.open();
		});

		$('#sercotec-footer-social-list').on('click', '.sercotec-social-row__icon-remove', function (event) {
			event.preventDefault();
			const $row = $(this).closest('.sercotec-social-row');
			setLibrarySelection($row, '');
		});
	}
})(jQuery);
