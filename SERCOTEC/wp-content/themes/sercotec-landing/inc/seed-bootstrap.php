<?php
/**
 * Carga e inserta los datos por defecto exportados en data/site-defaults.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_get_site_defaults_data(): array {
	static $data = null;

	if ( null !== $data ) {
		return $data;
	}

	$file = SERCOTEC_LANDING_DIR . '/data/site-defaults.php';

	if ( ! file_exists( $file ) ) {
		$data = array(
			'options' => array(),
			'cpts'    => array(),
		);

		return $data;
	}

	$loaded = include $file;

	$data = is_array( $loaded ) ? $loaded : array(
		'options' => array(),
		'cpts'    => array(),
	);

	return $data;
}

function sercotec_landing_get_site_default_option( string $option, $fallback ) {
	$defaults = sercotec_landing_get_site_defaults_data();
	$options  = $defaults['options'] ?? array();

	if ( array_key_exists( $option, $options ) ) {
		return $options[ $option ];
	}

	return $fallback;
}

function sercotec_landing_cpt_has_items( string $post_type ): bool {
	$existing = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	return ! empty( $existing );
}

function sercotec_landing_import_seed_media( array $media ): int {
	if ( empty( $media['upload_relative'] ) ) {
		return 0;
	}

	$relative = ltrim( (string) $media['upload_relative'], '/' );
	$source   = SERCOTEC_LANDING_DIR . '/data/media/' . $relative;

	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$upload_dir = wp_upload_dir();

	if ( ! empty( $upload_dir['error'] ) ) {
		return 0;
	}

	$destination = trailingslashit( $upload_dir['basedir'] ) . $relative;
	$dest_dir    = dirname( $destination );

	if ( ! is_dir( $dest_dir ) ) {
		wp_mkdir_p( $dest_dir );
	}

	if ( ! file_exists( $destination ) ) {
		copy( $source, $destination );
	}

	$filename = basename( $relative );

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_wp_attached_file',
					'value'   => $relative,
					'compare' => '=',
				),
			),
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$filetype = wp_check_filetype( $filename, null );

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
			'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => trailingslashit( $upload_dir['url'] ) . $relative,
		),
		$destination
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$metadata = wp_generate_attachment_metadata( $attachment_id, $destination );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	if ( ! empty( $media['alt'] ) ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( (string) $media['alt'] ) );
	}

	update_attached_file( $attachment_id, $destination );

	return (int) $attachment_id;
}

function sercotec_landing_seed_apply_options( bool $force = false ): void {
	$defaults = sercotec_landing_get_site_defaults_data();
	$options  = $defaults['options'] ?? array();

	foreach ( $options as $option => $value ) {
		if ( 'sercotec_footer_logo_media' === $option ) {
			continue;
		}

		if ( ! $force && false !== get_option( $option, false ) ) {
			continue;
		}

		update_option( $option, $value, false );
	}

	if ( ! empty( $options['sercotec_footer_logo_media'] ) && is_array( $options['sercotec_footer_logo_media'] ) ) {
		$logo_id = sercotec_landing_import_seed_media( $options['sercotec_footer_logo_media'] );

		if ( $logo_id > 0 && ( $force || ! get_option( 'sercotec_footer_logo_id', 0 ) ) ) {
			update_option( 'sercotec_footer_logo_id', $logo_id, false );
		}
	}
}

function sercotec_landing_seed_insert_cpt_items( string $post_type, array $items ): void {
	foreach ( $items as $item ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => $post_type,
				'post_title'   => (string) ( $item['title'] ?? '' ),
				'post_content' => (string) ( $item['content'] ?? '' ),
				'post_status'  => 'publish',
				'menu_order'   => (int) ( $item['menu_order'] ?? 0 ),
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		$meta = $item['meta'] ?? array();

		foreach ( $meta as $meta_key => $meta_value ) {
			if ( str_contains( (string) $meta_key, '_image_id' ) && is_array( $meta_value ) ) {
				$image_id = sercotec_landing_import_seed_media( $meta_value );

				if ( $image_id > 0 ) {
					update_post_meta( $post_id, $meta_key, $image_id );
				}

				continue;
			}

			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
}

function sercotec_landing_seed_default_content( bool $force_options = false ): void {
	$defaults = sercotec_landing_get_site_defaults_data();
	$cpts     = $defaults['cpts'] ?? array();

	sercotec_landing_seed_apply_options( $force_options );

	$map = array(
		'servicios'    => 'sercotec_servicio',
		'about_cards'  => 'sercotec_about_card',
		'testimonials' => 'sercotec_testimonio',
		'faqs'         => 'sercotec_faq',
	);

	foreach ( $map as $key => $post_type ) {
		if ( empty( $cpts[ $key ] ) || sercotec_landing_cpt_has_items( $post_type ) ) {
			continue;
		}

		sercotec_landing_seed_insert_cpt_items( $post_type, $cpts[ $key ] );
	}
}

function sercotec_landing_seed_on_theme_activation(): void {
	sercotec_landing_seed_default_content( false );
}
add_action( 'after_switch_theme', 'sercotec_landing_seed_on_theme_activation' );

function sercotec_landing_register_site_default_option_filters(): void {
	$defaults = sercotec_landing_get_site_defaults_data();
	$options  = $defaults['options'] ?? array();

	foreach ( array_keys( $options ) as $option ) {
		if ( 'sercotec_footer_logo_media' === $option ) {
			continue;
		}

		add_filter(
			'default_option_' . $option,
			static function ( $default, $option_name, $passed_default ) {
				unset( $default );

				return sercotec_landing_get_site_default_option( $option_name, $passed_default );
			},
			10,
			3
		);
	}
}
add_action( 'after_setup_theme', 'sercotec_landing_register_site_default_option_filters', 1 );
