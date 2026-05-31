<?php
/**
 * SERCOTEC Landing theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SERCOTEC_LANDING_VERSION', '1.31.1' );
define( 'SERCOTEC_LANDING_DIR', get_template_directory() );
define( 'SERCOTEC_LANDING_URI', get_template_directory_uri() );

require_once SERCOTEC_LANDING_DIR . '/inc/admin-site-config-menu.php';
require_once SERCOTEC_LANDING_DIR . '/inc/hero-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/cpt-about-cards.php';
require_once SERCOTEC_LANDING_DIR . '/inc/cpt-services.php';
require_once SERCOTEC_LANDING_DIR . '/inc/services-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/about-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/cpt-testimonials.php';
require_once SERCOTEC_LANDING_DIR . '/inc/testimonials-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/cpt-faq.php';
require_once SERCOTEC_LANDING_DIR . '/inc/faq-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/contact-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/contact-security.php';
require_once SERCOTEC_LANDING_DIR . '/inc/cpt-contact-messages.php';
require_once SERCOTEC_LANDING_DIR . '/inc/social-icon-library.php';
require_once SERCOTEC_LANDING_DIR . '/inc/footer-settings.php';
require_once SERCOTEC_LANDING_DIR . '/inc/rest-api.php';
require_once SERCOTEC_LANDING_DIR . '/inc/accessibility-toolbar.php';

function sercotec_landing_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'sercotec-service', 640, 400, true );
	add_image_size( 'sercotec-about-card', 640, 360, true );
	add_image_size( 'sercotec-logo', 240, 80, false );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Menú principal', 'sercotec-landing' ),
		)
	);
}
add_action( 'after_setup_theme', 'sercotec_landing_setup' );

function sercotec_landing_enqueue_theme_styles( array $deps = array() ): void {
	wp_enqueue_style(
		'sercotec-landing-main',
		SERCOTEC_LANDING_URI . '/assets/css/main.css',
		$deps,
		SERCOTEC_LANDING_VERSION
	);
}

function sercotec_landing_enqueue_assets() {
	wp_enqueue_style(
		'sercotec-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	sercotec_landing_enqueue_theme_styles( array( 'sercotec-google-fonts' ) );

	wp_enqueue_script(
		'sercotec-landing-main',
		SERCOTEC_LANDING_URI . '/assets/js/main.js',
		array(),
		SERCOTEC_LANDING_VERSION,
		true
	);

	if ( sercotec_landing_turnstile_is_enabled() ) {
		wp_enqueue_script(
			'cloudflare-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			array(),
			null,
			true
		);
	}

	wp_localize_script(
		'sercotec-landing-main',
		'sercotecLanding',
		array(
			'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
			'nonce'            => wp_create_nonce( 'sercotec_contact_form' ),
			'turnstileSiteKey' => sercotec_landing_get_turnstile_site_key(),
			'i18n'             => array(
				'nameRequired'    => __( 'Ingresa tu nombre completo.', 'sercotec-landing' ),
				'nameMax'         => __( 'El nombre no puede superar 120 caracteres.', 'sercotec-landing' ),
				'emailRequired'   => __( 'Ingresa tu email.', 'sercotec-landing' ),
				'emailInvalid'    => __( 'Ingresa un email válido.', 'sercotec-landing' ),
				'phoneInvalid'    => __( 'Ingresa un teléfono válido.', 'sercotec-landing' ),
				'phoneMax'        => __( 'El teléfono no puede superar 30 caracteres.', 'sercotec-landing' ),
				'serviceInvalid'  => __( 'Selecciona un servicio válido.', 'sercotec-landing' ),
				'messageRequired' => __( 'Ingresa un mensaje.', 'sercotec-landing' ),
				'messageMin'      => __( 'El mensaje debe tener al menos 10 caracteres.', 'sercotec-landing' ),
				'messageMax'      => __( 'El mensaje no puede superar 2000 caracteres.', 'sercotec-landing' ),
				'captchaRequired' => __( 'Completa la verificación de seguridad.', 'sercotec-landing' ),
				'submitLabel'     => __( 'Enviar mensaje', 'sercotec-landing' ),
				'submittingLabel' => __( 'Enviando...', 'sercotec-landing' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'sercotec_landing_enqueue_assets' );

function sercotec_landing_enqueue_admin_main_js( array $localize_scripts = array() ): void {
	wp_enqueue_script(
		'sercotec-landing-main',
		SERCOTEC_LANDING_URI . '/assets/js/main.js',
		array( 'jquery' ),
		SERCOTEC_LANDING_VERSION,
		true
	);

	foreach ( $localize_scripts as $object_name => $l10n_data ) {
		wp_localize_script( 'sercotec-landing-main', $object_name, $l10n_data );
	}
}

function sercotec_landing_enqueue_admin_assets( string $hook ): void {
	$screen = get_current_screen();

	if ( ! $screen || 'sercotec_servicio' !== $screen->post_type ) {
		return;
	}

	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_enqueue_media();

	sercotec_landing_enqueue_theme_styles();

	sercotec_landing_enqueue_admin_main_js();
}
add_action( 'admin_enqueue_scripts', 'sercotec_landing_enqueue_admin_assets' );

function sercotec_landing_default_menu() {
	echo '<ul class="navbar__menu">';
	echo '<li><a href="#inicio">' . esc_html__( 'Inicio', 'sercotec-landing' ) . '</a></li>';
	echo '<li><a href="#nosotros">' . esc_html__( 'Nosotros', 'sercotec-landing' ) . '</a></li>';
	echo '<li><a href="#servicios">' . esc_html__( 'Servicios', 'sercotec-landing' ) . '</a></li>';
	echo '<li><a href="#testimonios">' . esc_html__( 'Testimonios', 'sercotec-landing' ) . '</a></li>';
	echo '<li><a href="#faq">' . esc_html__( 'FAQ', 'sercotec-landing' ) . '</a></li>';
	echo '<li><a href="#contacto">' . esc_html__( 'Contacto', 'sercotec-landing' ) . '</a></li>';
	echo '</ul>';
}
