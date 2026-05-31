<?php
/**
 * Barra de accesibilidad — texto, contraste y escala de grises.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_a11y_early_script(): void {
	if ( is_admin() ) {
		return;
	}
	?>
	<script>
	(function () {
		try {
			var raw = localStorage.getItem('sercotec_a11y');
			if (!raw) {
				return;
			}
			var state = JSON.parse(raw);
			var root = document.documentElement;
			if (typeof state.fontScale === 'number') {
				root.dataset.a11yFontScale = String(state.fontScale);
			}
			if (state.highContrast) {
				root.classList.add('a11y-high-contrast');
			}
			if (state.grayscale) {
				root.classList.add('a11y-grayscale');
			}
		} catch (e) {}
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'sercotec_landing_a11y_early_script', 0 );

function sercotec_landing_render_accessibility_toolbar(): void {
	if ( is_admin() ) {
		return;
	}

	get_template_part( 'template-parts/accessibility-toolbar' );
}
