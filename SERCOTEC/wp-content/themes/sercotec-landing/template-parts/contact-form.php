<?php
$contact_services   = sercotec_landing_get_contact_form_services();
$turnstile_site_key = sercotec_landing_get_turnstile_site_key();
$turnstile_enabled  = sercotec_landing_turnstile_is_enabled();
?>

<section class="contact section" id="contacto" aria-labelledby="contact-title">
	<div class="container contact__inner">
		<div class="contact__info">
			<?php if ( sercotec_landing_get_contact_label() ) : ?>
				<span class="section__label"><?php echo esc_html( sercotec_landing_get_contact_label() ); ?></span>
			<?php endif; ?>
			<h2 class="section__title" id="contact-title">
				<?php echo esc_html( sercotec_landing_get_contact_title() ); ?>
			</h2>
			<?php if ( sercotec_landing_get_contact_description() ) : ?>
				<p><?php echo esc_html( sercotec_landing_get_contact_description() ); ?></p>
			<?php endif; ?>
			<ul class="contact__details">
				<?php if ( sercotec_landing_get_contact_email() ) : ?>
					<li>
						<strong><?php esc_html_e( 'Email', 'sercotec-landing' ); ?></strong>
						<a href="mailto:<?php echo esc_attr( sercotec_landing_get_contact_email() ); ?>">
							<?php echo esc_html( sercotec_landing_get_contact_email() ); ?>
						</a>
					</li>
				<?php endif; ?>
				<?php if ( sercotec_landing_get_contact_phone() ) : ?>
					<li>
						<strong><?php esc_html_e( 'Teléfono', 'sercotec-landing' ); ?></strong>
						<?php if ( sercotec_landing_get_contact_phone_href() ) : ?>
							<a href="<?php echo esc_attr( sercotec_landing_get_contact_phone_href() ); ?>">
								<?php echo esc_html( sercotec_landing_get_contact_phone() ); ?>
							</a>
						<?php else : ?>
							<span><?php echo esc_html( sercotec_landing_get_contact_phone() ); ?></span>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( sercotec_landing_get_contact_address() ) : ?>
					<li>
						<strong><?php esc_html_e( 'Dirección', 'sercotec-landing' ); ?></strong>
						<?php if ( sercotec_landing_get_contact_address_url() ) : ?>
							<a href="<?php echo esc_url( sercotec_landing_get_contact_address_url() ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( sercotec_landing_get_contact_address() ); ?>
							</a>
						<?php else : ?>
							<span><?php echo esc_html( sercotec_landing_get_contact_address() ); ?></span>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( sercotec_landing_get_contact_hours() ) : ?>
					<li>
						<strong><?php esc_html_e( 'Horario', 'sercotec-landing' ); ?></strong>
						<span><?php echo esc_html( sercotec_landing_get_contact_hours() ); ?></span>
					</li>
				<?php endif; ?>
			</ul>
		</div>

		<form class="contact-form" id="contact-form" novalidate>
			<div class="contact-form__field">
				<label for="contact-name"><?php esc_html_e( 'Nombre completo', 'sercotec-landing' ); ?> *</label>
				<input type="text" id="contact-name" name="name" required autocomplete="name" maxlength="120" aria-describedby="contact-name-error">
				<span class="contact-form__error" id="contact-name-error" role="alert"></span>
			</div>
			<div class="contact-form__row">
				<div class="contact-form__field">
					<label for="contact-email"><?php esc_html_e( 'Email', 'sercotec-landing' ); ?> *</label>
					<input type="email" id="contact-email" name="email" required autocomplete="email" maxlength="254" aria-describedby="contact-email-error">
					<span class="contact-form__error" id="contact-email-error" role="alert"></span>
				</div>
				<div class="contact-form__field">
					<label for="contact-phone"><?php esc_html_e( 'Teléfono', 'sercotec-landing' ); ?></label>
					<input type="tel" id="contact-phone" name="phone" autocomplete="tel" maxlength="30" aria-describedby="contact-phone-error">
					<span class="contact-form__error" id="contact-phone-error" role="alert"></span>
				</div>
			</div>
			<?php if ( ! empty( $contact_services ) ) : ?>
				<div class="contact-form__field">
					<label for="contact-service"><?php esc_html_e( 'Servicio de interés', 'sercotec-landing' ); ?></label>
					<select id="contact-service" name="service" aria-describedby="contact-service-error">
						<option value=""><?php esc_html_e( 'Selecciona un servicio', 'sercotec-landing' ); ?></option>
						<?php foreach ( $contact_services as $service_title ) : ?>
							<option value="<?php echo esc_attr( $service_title ); ?>"><?php echo esc_html( $service_title ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="contact-form__error" id="contact-service-error" role="alert"></span>
				</div>
			<?php endif; ?>
			<div class="contact-form__field">
				<label for="contact-message"><?php esc_html_e( 'Mensaje', 'sercotec-landing' ); ?> *</label>
				<textarea id="contact-message" name="message" rows="5" required maxlength="2000" aria-describedby="contact-message-error"></textarea>
				<span class="contact-form__error" id="contact-message-error" role="alert"></span>
			</div>
			<?php if ( $turnstile_enabled ) : ?>
				<div class="contact-form__field contact-form__field--captcha">
					<div
						class="cf-turnstile contact-form__turnstile"
						data-sitekey="<?php echo esc_attr( $turnstile_site_key ); ?>"
						data-theme="light"
						aria-describedby="contact-captcha-error"
					></div>
					<span class="contact-form__error" id="contact-captcha-error" role="alert"></span>
				</div>
			<?php endif; ?>
			<button class="btn btn--primary btn--lg contact-form__submit" type="submit">
				<?php esc_html_e( 'Enviar mensaje', 'sercotec-landing' ); ?>
			</button>
			<p class="contact-form__feedback" id="contact-feedback" role="status" aria-live="polite"></p>
		</form>
	</div>
</section>
