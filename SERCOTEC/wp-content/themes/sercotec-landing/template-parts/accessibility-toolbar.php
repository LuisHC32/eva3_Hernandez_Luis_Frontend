<?php
/**
 * Barra flotante de opciones de accesibilidad.
 */
?>
<aside class="a11y-toolbar" data-a11y-toolbar aria-label="<?php esc_attr_e( 'Opciones de accesibilidad', 'sercotec-landing' ); ?>">
	<button
		type="button"
		class="a11y-toolbar__toggle"
		data-a11y-toggle
		aria-expanded="false"
		aria-controls="a11y-toolbar-panel"
	>
		<span class="a11y-toolbar__toggle-icon" aria-hidden="true">
			<img
				src="<?php echo esc_url( SERCOTEC_LANDING_URI . '/assets/icons/accessibility.png' ); ?>"
				alt=""
				width="28"
				height="28"
				decoding="async"
			>
		</span>
		<span class="a11y-toolbar__toggle-label"><?php esc_html_e( 'Accesibilidad', 'sercotec-landing' ); ?></span>
	</button>
	<div class="a11y-toolbar__panel" id="a11y-toolbar-panel" data-a11y-panel hidden>
		<p class="a11y-toolbar__title"><?php esc_html_e( 'Accesibilidad', 'sercotec-landing' ); ?></p>
		<div class="a11y-toolbar__group" role="group" aria-label="<?php esc_attr_e( 'Tamaño de texto', 'sercotec-landing' ); ?>">
			<button type="button" class="a11y-toolbar__btn" data-a11y-font-decrease aria-label="<?php esc_attr_e( 'Disminuir tamaño de texto', 'sercotec-landing' ); ?>">
				<?php esc_html_e( 'A−', 'sercotec-landing' ); ?>
			</button>
			<button type="button" class="a11y-toolbar__btn" data-a11y-font-increase aria-label="<?php esc_attr_e( 'Aumentar tamaño de texto', 'sercotec-landing' ); ?>">
				<?php esc_html_e( 'A+', 'sercotec-landing' ); ?>
			</button>
		</div>
		<button
			type="button"
			class="a11y-toolbar__btn a11y-toolbar__btn--wide"
			data-a11y-contrast
			aria-pressed="false"
		>
			<?php esc_html_e( 'Alto contraste', 'sercotec-landing' ); ?>
		</button>
		<button
			type="button"
			class="a11y-toolbar__btn a11y-toolbar__btn--wide"
			data-a11y-grayscale
			aria-pressed="false"
		>
			<?php esc_html_e( 'Escala de grises', 'sercotec-landing' ); ?>
		</button>
		<button type="button" class="a11y-toolbar__btn a11y-toolbar__btn--reset" data-a11y-reset>
			<?php esc_html_e( 'Restablecer', 'sercotec-landing' ); ?>
		</button>
	</div>
</aside>
