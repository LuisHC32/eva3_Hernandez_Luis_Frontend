<?php
/**
 * Configuración editable del footer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_footer_settings_menu(): void {
	add_menu_page(
		__( 'Footer', 'sercotec-landing' ),
		__( 'Footer', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-footer',
		'sercotec_landing_footer_settings_page',
		'dashicons-layout',
		27
	);
}
add_action( 'admin_menu', 'sercotec_landing_footer_settings_menu' );

function sercotec_landing_register_footer_settings(): void {
	$fields = array(
		'sercotec_footer_logo_letter'  => array( 'sanitize_text_field', 'sercotec_landing_default_footer_logo_letter' ),
		'sercotec_footer_brand_name'    => array( 'sanitize_text_field', 'sercotec_landing_default_footer_brand_name' ),
		'sercotec_footer_description'   => array( 'sanitize_textarea_field', 'sercotec_landing_default_footer_description' ),
		'sercotec_footer_links_title'   => array( 'sanitize_text_field', 'sercotec_landing_default_footer_links_title' ),
		'sercotec_footer_links'         => array( 'sercotec_landing_sanitize_footer_links', 'sercotec_landing_default_footer_links' ),
		'sercotec_footer_social_title'  => array( 'sanitize_text_field', 'sercotec_landing_default_footer_social_title' ),
		'sercotec_footer_copyright'     => array( 'sanitize_text_field', 'sercotec_landing_default_footer_copyright' ),
	);

	foreach ( $fields as $option => $config ) {
		register_setting(
			'sercotec_footer_settings',
			$option,
			array(
				'type'              => 'string',
				'sanitize_callback' => $config[0],
				'default'           => call_user_func( $config[1] ),
			)
		);
	}

	register_setting(
		'sercotec_footer_settings',
		'sercotec_footer_logo_id',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_footer_logo_id',
			'default'           => 0,
		)
	);

	register_setting(
		'sercotec_footer_settings',
		'sercotec_footer_social_links',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'sercotec_landing_sanitize_footer_social_links',
			'default'           => sercotec_landing_default_footer_social_links_array(),
		)
	);
}
add_action( 'admin_init', 'sercotec_landing_register_footer_settings' );

function sercotec_landing_footer_settings_assets( string $hook ): void {
	if ( 'toplevel_page_sercotec-footer' !== $hook ) {
		return;
	}

	wp_enqueue_media();

	sercotec_landing_enqueue_theme_styles();

	sercotec_landing_enqueue_admin_main_js(
		array(
			'sercotecFooterAdmin' => array(
				'rowTemplate' => sercotec_landing_footer_social_row_template(),
				'labels'      => array(
					'selectIcon'  => __( 'Seleccionar logo', 'sercotec-landing' ),
					'removeIcon'  => __( 'Quitar logo', 'sercotec-landing' ),
					'noIcon'      => __( 'Sin logo', 'sercotec-landing' ),
					'removeRow'   => __( 'Eliminar red', 'sercotec-landing' ),
					'mediaTitle'  => __( 'Seleccionar logo de red social', 'sercotec-landing' ),
					'mediaButton' => __( 'Usar este logo', 'sercotec-landing' ),
				),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'sercotec_landing_footer_settings_assets' );

function sercotec_landing_sanitize_footer_logo_id( $value ): int {
	return absint( $value );
}

function sercotec_landing_default_footer_logo_letter(): string {
	return 'S';
}

function sercotec_landing_default_footer_brand_name(): string {
	return 'SERCOTEC';
}

function sercotec_landing_default_footer_description(): string {
	return __( 'Servicio de Cooperación Técnica · Ministerio de Economía, Fomento y Turismo.', 'sercotec-landing' );
}

function sercotec_landing_default_footer_links_title(): string {
	return __( 'Enlaces', 'sercotec-landing' );
}

function sercotec_landing_default_footer_links(): string {
	return implode(
		"\n",
		array(
			__( 'Nosotros', 'sercotec-landing' ) . ' | #nosotros',
			__( 'Servicios', 'sercotec-landing' ) . ' | #servicios',
			'FAQ | #faq',
			__( 'Contacto', 'sercotec-landing' ) . ' | #contacto',
		)
	);
}

function sercotec_landing_default_footer_social_title(): string {
	return __( 'Síguenos', 'sercotec-landing' );
}

function sercotec_landing_default_footer_social_links_array(): array {
	return array(
		array(
			'label'    => 'LinkedIn',
			'url'      => '#',
			'icon_key' => 'linkedin',
			'icon_id'  => 0,
		),
		array(
			'label'    => 'Instagram',
			'url'      => '#',
			'icon_key' => 'instagram',
			'icon_id'  => 0,
		),
		array(
			'label'    => 'YouTube',
			'url'      => '#',
			'icon_key' => 'youtube',
			'icon_id'  => 0,
		),
	);
}

function sercotec_landing_default_footer_copyright(): string {
	return __( 'SERCOTEC. Todos los derechos reservados.', 'sercotec-landing' );
}

function sercotec_landing_get_footer_option( string $option, callable $default_fn ): string {
	$value = get_option( $option, call_user_func( $default_fn ) );

	return is_string( $value ) && '' !== $value ? $value : call_user_func( $default_fn );
}

function sercotec_landing_get_footer_option_allow_empty( string $option, callable $default_fn ): string {
	$not_set = '__sercotec_option_not_set__';
	$value   = get_option( $option, $not_set );

	if ( $not_set === $value ) {
		return call_user_func( $default_fn );
	}

	return is_string( $value ) ? $value : call_user_func( $default_fn );
}

function sercotec_landing_sanitize_footer_links( $value ): string {
	$links = sercotec_landing_parse_footer_links( (string) $value );

	if ( empty( $links ) ) {
		return sercotec_landing_default_footer_links();
	}

	return sercotec_landing_format_footer_links( $links );
}

function sercotec_landing_sanitize_footer_social_links( $value ): array {
	if ( is_string( $value ) ) {
		$value = sercotec_landing_normalize_stored_social_links( $value );
	}

	if ( ! is_array( $value ) ) {
		return sercotec_landing_default_footer_social_links_array();
	}

	$links = array();

	foreach ( $value as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$label = sanitize_text_field( $row['label'] ?? '' );

		$url = esc_url_raw( $row['url'] ?? '' );

		if ( ! $url ) {
			$url = sanitize_text_field( $row['url'] ?? '' );
		}

		$icon_id  = absint( $row['icon_id'] ?? 0 );
		$icon_key = sanitize_key( $row['icon_key'] ?? '' );

		if ( $icon_id ) {
			$icon_key = '';
		} elseif ( $icon_key && ! sercotec_landing_social_icon_exists( $icon_key ) ) {
			$icon_key = '';
		}

		if ( ! $icon_id && ! $icon_key && '' === $url ) {
			continue;
		}

		$links[] = array(
			'label'    => $label,
			'url'      => $url,
			'icon_key' => $icon_key,
			'icon_id'  => $icon_id,
		);
	}

	return ! empty( $links ) ? $links : sercotec_landing_default_footer_social_links_array();
}

function sercotec_landing_normalize_stored_social_links( $stored ): array {
	if ( is_array( $stored ) ) {
		return sercotec_landing_sanitize_footer_social_links( $stored );
	}

	$raw = trim( (string) $stored );

	if ( '' === $raw ) {
		return sercotec_landing_default_footer_social_links_array();
	}

	if ( str_starts_with( $raw, '[' ) ) {
		$decoded = json_decode( $raw, true );

		if ( is_array( $decoded ) ) {
			return sercotec_landing_sanitize_footer_social_links( $decoded );
		}
	}

	return sercotec_landing_convert_legacy_social_lines( $raw );
}

function sercotec_landing_convert_legacy_social_lines( string $raw ): array {
	$links  = array();
	$lines  = preg_split( '/\r\n|\r|\n/', $raw );

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 3 ) );

		if ( count( $parts ) < 2 || '' === $parts[0] ) {
			continue;
		}

		$url = esc_url_raw( $parts[1] ) ?: sanitize_text_field( $parts[1] );
		$icon_key = sercotec_landing_guess_social_icon_key( $parts[0] );

		$links[] = array(
			'label'    => sanitize_text_field( $parts[0] ),
			'url'      => $url,
			'icon_key' => $icon_key,
			'icon_id'  => 0,
		);
	}

	return ! empty( $links ) ? $links : sercotec_landing_default_footer_social_links_array();
}

function sercotec_landing_enrich_footer_social_links( array $links ): array {
	$enriched = array();

	foreach ( $links as $link ) {
		$icon_id  = absint( $link['icon_id'] ?? 0 );
		$icon_key = sanitize_key( $link['icon_key'] ?? '' );
		$icon_url = '';
		$icon_svg = '';

		if ( $icon_id ) {
			$icon_url = (string) wp_get_attachment_image_url( $icon_id, 'thumbnail' );
			$icon_key = '';
		} elseif ( $icon_key && sercotec_landing_social_icon_exists( $icon_key ) ) {
			$icon_svg = sercotec_landing_get_social_icon_svg( $icon_key );
		} elseif ( '' === $icon_key ) {
			$guessed_key = sercotec_landing_guess_social_icon_key( (string) ( $link['label'] ?? '' ) );

			if ( $guessed_key ) {
				$icon_key = $guessed_key;
				$icon_svg = sercotec_landing_get_social_icon_svg( $icon_key );
			}
		}

		$enriched[] = array(
			'label'    => sanitize_text_field( $link['label'] ?? '' ),
			'url'      => (string) ( $link['url'] ?? '' ),
			'icon_key' => $icon_key,
			'icon_id'  => $icon_id,
			'icon_url' => $icon_url,
			'icon_svg' => $icon_svg,
		);
	}

	return $enriched;
}

function sercotec_landing_parse_footer_links( string $raw ): array {
	$links = array();
	$lines = preg_split( '/\r\n|\r|\n/', $raw );

	foreach ( $lines as $line ) {
		$line = trim( $line );

		if ( '' === $line ) {
			continue;
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );

		if ( count( $parts ) < 2 || '' === $parts[0] || '' === $parts[1] ) {
			continue;
		}

		$links[] = array(
			'label' => sanitize_text_field( $parts[0] ),
			'url'   => esc_url_raw( $parts[1] ) ?: sanitize_text_field( $parts[1] ),
		);
	}

	return $links;
}

function sercotec_landing_format_footer_links( array $links ): string {
	$lines = array();

	foreach ( $links as $link ) {
		$lines[] = $link['label'] . ' | ' . $link['url'];
	}

	return implode( "\n", $lines );
}

function sercotec_landing_get_footer_logo_letter(): string {
	return sercotec_landing_get_footer_option( 'sercotec_footer_logo_letter', 'sercotec_landing_default_footer_logo_letter' );
}

function sercotec_landing_get_footer_logo_id(): int {
	return sercotec_landing_sanitize_footer_logo_id( get_option( 'sercotec_footer_logo_id', 0 ) );
}

function sercotec_landing_get_footer_logo_url(): string {
	$logo_id = sercotec_landing_get_footer_logo_id();

	if ( ! $logo_id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $logo_id, 'sercotec-logo' );

	return $url ? $url : '';
}

function sercotec_landing_get_footer_brand_name(): string {
	return sercotec_landing_get_footer_option_allow_empty( 'sercotec_footer_brand_name', 'sercotec_landing_default_footer_brand_name' );
}

function sercotec_landing_get_footer_brand_alt(): string {
	$brand_name = sercotec_landing_get_footer_brand_name();

	if ( '' !== $brand_name ) {
		return $brand_name;
	}

	return get_bloginfo( 'name', 'display' ) ?: sercotec_landing_default_footer_brand_name();
}

function sercotec_landing_get_footer_description(): string {
	return sercotec_landing_get_footer_option( 'sercotec_footer_description', 'sercotec_landing_default_footer_description' );
}

function sercotec_landing_get_footer_links_title(): string {
	return sercotec_landing_get_footer_option( 'sercotec_footer_links_title', 'sercotec_landing_default_footer_links_title' );
}

function sercotec_landing_get_footer_links(): array {
	return sercotec_landing_parse_footer_links(
		sercotec_landing_get_footer_option( 'sercotec_footer_links', 'sercotec_landing_default_footer_links' )
	);
}

function sercotec_landing_get_footer_social_title(): string {
	return sercotec_landing_get_footer_option( 'sercotec_footer_social_title', 'sercotec_landing_default_footer_social_title' );
}

function sercotec_landing_get_footer_social_links(): array {
	$stored = get_option( 'sercotec_footer_social_links', sercotec_landing_default_footer_social_links_array() );

	return sercotec_landing_enrich_footer_social_links(
		sercotec_landing_normalize_stored_social_links( $stored )
	);
}

function sercotec_landing_footer_social_row_template(): string {
	ob_start();
	sercotec_landing_render_footer_social_row( '__index__', array() );
	return ob_get_clean();
}

function sercotec_landing_render_footer_social_row( $index, array $row ): void {
	$label    = $row['label'] ?? '';
	$url      = $row['url'] ?? '';
	$icon_key = sanitize_key( $row['icon_key'] ?? '' );
	$icon_id  = absint( $row['icon_id'] ?? 0 );

	if ( $icon_id ) {
		$icon_key = '';
	}
	?>
	<div class="sercotec-social-row" data-index="<?php echo esc_attr( (string) $index ); ?>">
		<div class="sercotec-social-row__fields">
			<p>
				<label><?php esc_html_e( 'Nombre', 'sercotec-landing' ); ?> <span class="description"><?php esc_html_e( '(opcional)', 'sercotec-landing' ); ?></span></label>
				<input
					type="text"
					name="sercotec_footer_social_links[<?php echo esc_attr( (string) $index ); ?>][label]"
					value="<?php echo esc_attr( $label ); ?>"
					class="regular-text sercotec-social-row__label"
					placeholder="<?php esc_attr_e( 'LinkedIn', 'sercotec-landing' ); ?>"
				>
			</p>
			<p>
				<label><?php esc_html_e( 'URL', 'sercotec-landing' ); ?></label>
				<input
					type="url"
					name="sercotec_footer_social_links[<?php echo esc_attr( (string) $index ); ?>][url]"
					value="<?php echo esc_attr( $url ); ?>"
					class="large-text"
					placeholder="https://"
				>
			</p>
		</div>
		<div class="sercotec-social-row__icon">
			<label><?php esc_html_e( 'Logo', 'sercotec-landing' ); ?></label>
			<input
				type="hidden"
				class="sercotec-social-row__icon-key"
				name="sercotec_footer_social_links[<?php echo esc_attr( (string) $index ); ?>][icon_key]"
				value="<?php echo esc_attr( $icon_key ); ?>"
			>
			<input
				type="hidden"
				class="sercotec-social-row__icon-id"
				name="sercotec_footer_social_links[<?php echo esc_attr( (string) $index ); ?>][icon_id]"
				value="<?php echo esc_attr( (string) $icon_id ); ?>"
			>
			<?php sercotec_landing_render_social_icon_picker( $icon_key, $icon_id ); ?>
			<div class="sercotec-social-row__icon-preview">
				<?php sercotec_landing_render_footer_social_icon_preview( $icon_key, $icon_id ); ?>
			</div>
			<div class="sercotec-social-row__icon-actions">
				<button type="button" class="button button-secondary sercotec-social-row__icon-select">
					<?php esc_html_e( 'Subir logo personalizado', 'sercotec-landing' ); ?>
				</button>
				<button type="button" class="button button-link-delete sercotec-social-row__icon-remove" <?php disabled( ! $icon_id && ! $icon_key ); ?>>
					<?php esc_html_e( 'Quitar logo', 'sercotec-landing' ); ?>
				</button>
			</div>
			<p class="description"><?php esc_html_e( 'Elige un logo de la biblioteca o sube uno propio.', 'sercotec-landing' ); ?></p>
		</div>
		<button type="button" class="button-link-delete sercotec-social-row__remove">
			<?php esc_html_e( 'Eliminar red', 'sercotec-landing' ); ?>
		</button>
	</div>
	<?php
}

function sercotec_landing_get_footer_copyright(): string {
	return sercotec_landing_get_footer_option( 'sercotec_footer_copyright', 'sercotec_landing_default_footer_copyright' );
}

function sercotec_landing_footer_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$logo_letter   = sercotec_landing_get_footer_logo_letter();
	$logo_id       = sercotec_landing_get_footer_logo_id();
	$logo_url      = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	$brand_name    = sercotec_landing_get_footer_brand_name();
	$description   = sercotec_landing_get_footer_description();
	$links_title   = sercotec_landing_get_footer_links_title();
	$links         = sercotec_landing_get_footer_option( 'sercotec_footer_links', 'sercotec_landing_default_footer_links' );
	$social_title  = sercotec_landing_get_footer_social_title();
	$social_links  = sercotec_landing_get_footer_social_links();
	$copyright     = sercotec_landing_get_footer_copyright();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración del footer', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza la marca, enlaces, redes sociales y copyright del pie de página.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_footer_settings' ); ?>

			<h2><?php esc_html_e( 'Marca', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_footer_logo_id"><?php esc_html_e( 'Logo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<div class="sercotec-service-image-field">
							<input type="hidden" id="sercotec_footer_logo_id" name="sercotec_footer_logo_id" value="<?php echo esc_attr( (string) $logo_id ); ?>">
							<div id="sercotec-footer-logo-preview" class="sercotec-service-image-field__preview">
								<?php if ( $logo_url ) : ?>
									<img src="<?php echo esc_url( $logo_url ); ?>" alt="">
								<?php else : ?>
									<span class="sercotec-service-image-field__placeholder"><?php esc_html_e( 'Sin logo seleccionado', 'sercotec-landing' ); ?></span>
								<?php endif; ?>
							</div>
							<div class="sercotec-service-image-field__actions">
								<button type="button" class="button button-secondary" id="sercotec-footer-logo-select">
									<?php esc_html_e( 'Seleccionar logo', 'sercotec-landing' ); ?>
								</button>
								<button type="button" class="button button-link-delete" id="sercotec-footer-logo-remove" <?php disabled( ! $logo_id ); ?>>
									<?php esc_html_e( 'Quitar logo', 'sercotec-landing' ); ?>
								</button>
							</div>
							<p class="description"><?php esc_html_e( 'Si subes un logo, reemplaza la letra del logo en el navbar y el footer.', 'sercotec-landing' ); ?></p>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_footer_logo_letter"><?php esc_html_e( 'Letra del logo', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_footer_logo_letter" name="sercotec_footer_logo_letter" value="<?php echo esc_attr( $logo_letter ); ?>" class="small-text" maxlength="2">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_footer_brand_name"><?php esc_html_e( 'Nombre', 'sercotec-landing' ); ?> <span class="description"><?php esc_html_e( '(opcional)', 'sercotec-landing' ); ?></span></label>
					</th>
					<td>
						<input type="text" id="sercotec_footer_brand_name" name="sercotec_footer_brand_name" value="<?php echo esc_attr( $brand_name ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( 'Déjalo vacío si tu logo ya incluye el nombre de la marca.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_footer_description"><?php esc_html_e( 'Descripción', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea id="sercotec_footer_description" name="sercotec_footer_description" rows="3" class="large-text"><?php echo esc_textarea( $description ); ?></textarea>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Enlaces', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_footer_links_title"><?php esc_html_e( 'Título de columna', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_footer_links_title" name="sercotec_footer_links_title" value="<?php echo esc_attr( $links_title ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_footer_links"><?php esc_html_e( 'Lista de enlaces', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea id="sercotec_footer_links" name="sercotec_footer_links" rows="6" class="large-text code"><?php echo esc_textarea( $links ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Un enlace por línea con el formato: Texto | URL', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Redes sociales', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_footer_social_title"><?php esc_html_e( 'Título de columna', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_footer_social_title" name="sercotec_footer_social_title" value="<?php echo esc_attr( $social_title ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Redes', 'sercotec-landing' ); ?></th>
					<td>
						<div id="sercotec-footer-social-list" class="sercotec-social-list">
							<?php foreach ( $social_links as $index => $social ) : ?>
								<?php sercotec_landing_render_footer_social_row( $index, $social ); ?>
							<?php endforeach; ?>
						</div>
						<p>
							<button type="button" class="button button-secondary" id="sercotec-footer-social-add">
								<?php esc_html_e( 'Agregar red social', 'sercotec-landing' ); ?>
							</button>
						</p>
						<p class="description"><?php esc_html_e( 'Elige un logo de la biblioteca o sube uno propio para cada red.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Copyright', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_footer_copyright"><?php esc_html_e( 'Texto legal', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_footer_copyright" name="sercotec_footer_copyright" value="<?php echo esc_attr( $copyright ); ?>" class="large-text">
						<p class="description"><?php esc_html_e( 'Se muestra después del año. Ejemplo: SERCOTEC. Todos los derechos reservados.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
