<?php
/**
 * Configuración editable de la sección Contacto.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sercotec_landing_contact_settings_menu(): void {
	add_menu_page(
		__( 'Contacto', 'sercotec-landing' ),
		__( 'Contacto', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-contact',
		'sercotec_landing_contact_settings_page',
		'dashicons-email',
		26
	);

	add_submenu_page(
		'sercotec-contact',
		__( 'Configuración de sección', 'sercotec-landing' ),
		__( 'Configuración de sección', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-contact',
		'sercotec_landing_contact_settings_page'
	);

	add_submenu_page(
		'sercotec-contact',
		__( 'CAPTCHA y seguridad', 'sercotec-landing' ),
		__( 'CAPTCHA y seguridad', 'sercotec-landing' ),
		'edit_posts',
		'sercotec-contact-security',
		'sercotec_landing_contact_security_settings_page'
	);
}
add_action( 'admin_menu', 'sercotec_landing_contact_settings_menu', 9 );

function sercotec_landing_register_contact_settings(): void {
	$fields = array(
		'sercotec_contact_label'       => array( 'sanitize_text_field', 'sercotec_landing_default_contact_label' ),
		'sercotec_contact_title'       => array( 'sanitize_text_field', 'sercotec_landing_default_contact_title' ),
		'sercotec_contact_description' => array( 'sanitize_textarea_field', 'sercotec_landing_default_contact_description' ),
		'sercotec_contact_email'       => array( 'sanitize_email', 'sercotec_landing_default_contact_email' ),
		'sercotec_contact_phone'       => array( 'sanitize_text_field', 'sercotec_landing_default_contact_phone' ),
		'sercotec_contact_address'     => array( 'sanitize_textarea_field', 'sercotec_landing_default_contact_address' ),
		'sercotec_contact_address_url' => array( 'esc_url_raw', 'sercotec_landing_default_contact_address_url' ),
		'sercotec_contact_hours'       => array( 'sanitize_text_field', 'sercotec_landing_default_contact_hours' ),
	);

	foreach ( $fields as $option => $config ) {
		register_setting(
			'sercotec_contact_settings',
			$option,
			array(
				'type'              => 'string',
				'sanitize_callback' => $config[0],
				'default'           => call_user_func( $config[1] ),
			)
		);
	}

	register_setting(
		'sercotec_contact_settings',
		'sercotec_contact_rate_limit_max',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_contact_rate_limit_max',
			'default'           => 3,
		)
	);

	register_setting(
		'sercotec_contact_settings',
		'sercotec_contact_rate_limit_window',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'sercotec_landing_sanitize_contact_rate_limit_window',
			'default'           => 15,
		)
	);

	register_setting(
		'sercotec_contact_settings',
		'sercotec_contact_turnstile_site_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);

	register_setting(
		'sercotec_contact_settings',
		'sercotec_contact_turnstile_secret_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'sercotec_landing_register_contact_settings' );

function sercotec_landing_sanitize_contact_rate_limit_max( $value ): int {
	return max( 1, min( 20, absint( $value ) ) );
}

function sercotec_landing_sanitize_contact_rate_limit_window( $value ): int {
	return max( 1, min( 1440, absint( $value ) ) );
}

function sercotec_landing_default_contact_label(): string {
	return __( 'Contacto', 'sercotec-landing' );
}

function sercotec_landing_default_contact_title(): string {
	return __( 'Cuéntanos sobre tu proyecto', 'sercotec-landing' );
}

function sercotec_landing_default_contact_description(): string {
	return __( 'Completa el formulario y un ejecutivo SERCOTEC te contactará para orientarte sobre programas disponibles en tu región.', 'sercotec-landing' );
}

function sercotec_landing_default_contact_email(): string {
	return 'contacto@sercotec.cl';
}

function sercotec_landing_default_contact_phone(): string {
	return '+56 2 1234 5678';
}

function sercotec_landing_default_contact_address(): string {
	return '';
}

function sercotec_landing_default_contact_address_url(): string {
	return '';
}

function sercotec_landing_default_contact_hours(): string {
	return __( 'Lunes a viernes, 9:00 – 18:00 hrs', 'sercotec-landing' );
}

function sercotec_landing_get_contact_option( string $option, callable $default_fn ): string {
	$value = get_option( $option, call_user_func( $default_fn ) );

	return is_string( $value ) && '' !== $value ? $value : call_user_func( $default_fn );
}

function sercotec_landing_get_contact_label(): string {
	return sercotec_landing_get_contact_option( 'sercotec_contact_label', 'sercotec_landing_default_contact_label' );
}

function sercotec_landing_get_contact_title(): string {
	return sercotec_landing_get_contact_option( 'sercotec_contact_title', 'sercotec_landing_default_contact_title' );
}

function sercotec_landing_get_contact_description(): string {
	return sercotec_landing_get_contact_option( 'sercotec_contact_description', 'sercotec_landing_default_contact_description' );
}

function sercotec_landing_get_contact_email(): string {
	$email = sercotec_landing_get_contact_option( 'sercotec_contact_email', 'sercotec_landing_default_contact_email' );

	return is_email( $email ) ? $email : sercotec_landing_default_contact_email();
}

function sercotec_landing_get_contact_phone(): string {
	return sercotec_landing_get_contact_option( 'sercotec_contact_phone', 'sercotec_landing_default_contact_phone' );
}

function sercotec_landing_get_contact_address(): string {
	return trim( sercotec_landing_get_contact_option( 'sercotec_contact_address', 'sercotec_landing_default_contact_address' ) );
}

function sercotec_landing_get_contact_address_url(): string {
	$url = sercotec_landing_get_contact_option( 'sercotec_contact_address_url', 'sercotec_landing_default_contact_address_url' );

	return esc_url( $url );
}

function sercotec_landing_get_contact_hours(): string {
	return sercotec_landing_get_contact_option( 'sercotec_contact_hours', 'sercotec_landing_default_contact_hours' );
}

function sercotec_landing_get_contact_phone_href(): string {
	$phone = preg_replace( '/[^\d+]/', '', sercotec_landing_get_contact_phone() );

	return $phone ? 'tel:' . $phone : '';
}

function sercotec_landing_contact_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$label       = sercotec_landing_get_contact_label();
	$title       = sercotec_landing_get_contact_title();
	$description = sercotec_landing_get_contact_description();
	$email       = sercotec_landing_get_contact_email();
	$phone       = sercotec_landing_get_contact_phone();
	$address     = sercotec_landing_get_contact_address();
	$address_url = sercotec_landing_get_contact_address_url();
	$hours       = sercotec_landing_get_contact_hours();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Configuración de la sección Contacto', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Personaliza los textos y datos de contacto que se muestran en la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_contact_settings' ); ?>

			<h2><?php esc_html_e( 'Encabezado', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_contact_label"><?php esc_html_e( 'Etiqueta', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_contact_label" name="sercotec_contact_label" value="<?php echo esc_attr( $label ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_title"><?php esc_html_e( 'Título', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_contact_title" name="sercotec_contact_title" value="<?php echo esc_attr( $title ); ?>" class="large-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_description"><?php esc_html_e( 'Descripción', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea id="sercotec_contact_description" name="sercotec_contact_description" rows="3" class="large-text"><?php echo esc_textarea( $description ); ?></textarea>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Datos de contacto', 'sercotec-landing' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_contact_email"><?php esc_html_e( 'Email', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="email" id="sercotec_contact_email" name="sercotec_contact_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_phone"><?php esc_html_e( 'Teléfono', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_contact_phone" name="sercotec_contact_phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_address"><?php esc_html_e( 'Dirección', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<textarea id="sercotec_contact_address" name="sercotec_contact_address" rows="2" class="large-text"><?php echo esc_textarea( $address ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_address_url"><?php esc_html_e( 'Enlace de mapa', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="url" id="sercotec_contact_address_url" name="sercotec_contact_address_url" value="<?php echo esc_attr( $address_url ); ?>" class="large-text" placeholder="https://maps.google.com/...">
						<p class="description"><?php esc_html_e( 'Opcional. Enlace a Google Maps u otra ubicación.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_hours"><?php esc_html_e( 'Horario', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="text" id="sercotec_contact_hours" name="sercotec_contact_hours" value="<?php echo esc_attr( $hours ); ?>" class="large-text">
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}

function sercotec_landing_contact_security_settings_page(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$rate_max             = sercotec_landing_get_contact_rate_limit_max();
	$rate_window          = (int) get_option( 'sercotec_contact_rate_limit_window', 15 );
	$turnstile_site_key   = sercotec_landing_get_turnstile_site_key();
	$turnstile_secret_key = sercotec_landing_get_turnstile_secret_key();
	$turnstile_enabled    = sercotec_landing_turnstile_is_enabled();
	$keys_from_constants  = defined( 'SERCOTEC_TURNSTILE_SITE_KEY' ) || defined( 'SERCOTEC_TURNSTILE_SECRET_KEY' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'CAPTCHA y seguridad del formulario', 'sercotec-landing' ); ?></h1>
		<p><?php esc_html_e( 'Configura la protección anti-bots del formulario de contacto en la landing.', 'sercotec-landing' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'sercotec_contact_settings' ); ?>

			<h2><?php esc_html_e( 'Cloudflare Turnstile', 'sercotec-landing' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: Cloudflare Turnstile URL */
					esc_html__( 'Ingresa las claves obtenidas en %s para activar el CAPTCHA en el formulario.', 'sercotec-landing' ),
					'<a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank" rel="noopener noreferrer">Cloudflare Turnstile</a>'
				);
				?>
			</p>

			<?php if ( $keys_from_constants ) : ?>
				<div class="notice notice-info inline">
					<p><?php esc_html_e( 'Las claves están definidas en wp-config.php y tienen prioridad sobre los valores guardados aquí.', 'sercotec-landing' ); ?></p>
				</div>
			<?php endif; ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_contact_turnstile_site_key"><?php esc_html_e( 'Site Key', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							id="sercotec_contact_turnstile_site_key"
							name="sercotec_contact_turnstile_site_key"
							value="<?php echo esc_attr( $turnstile_site_key ); ?>"
							class="large-text code"
							autocomplete="off"
							placeholder="1x00000000000000000000AA"
							<?php echo $keys_from_constants ? 'readonly' : ''; ?>
						>
						<p class="description"><?php esc_html_e( 'Clave pública. Se usa en el formulario visible del sitio.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_turnstile_secret_key"><?php esc_html_e( 'Secret Key', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input
							type="password"
							id="sercotec_contact_turnstile_secret_key"
							name="sercotec_contact_turnstile_secret_key"
							value="<?php echo esc_attr( $turnstile_secret_key ); ?>"
							class="large-text code"
							autocomplete="new-password"
							placeholder="1x0000000000000000000000000000000AA"
							<?php echo $keys_from_constants ? 'readonly' : ''; ?>
						>
						<p class="description"><?php esc_html_e( 'Clave privada. Solo se usa en el servidor para validar el CAPTCHA.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<?php if ( $turnstile_enabled ) : ?>
				<p>
					<span class="dashicons dashicons-yes-alt" style="color:#00a32a;vertical-align:middle;"></span>
					<strong><?php esc_html_e( 'CAPTCHA activo', 'sercotec-landing' ); ?></strong> —
					<?php esc_html_e( 'El widget de verificación se muestra en el formulario de contacto.', 'sercotec-landing' ); ?>
				</p>
			<?php else : ?>
				<p>
					<span class="dashicons dashicons-warning" style="color:#dba617;vertical-align:middle;"></span>
					<strong><?php esc_html_e( 'CAPTCHA inactivo', 'sercotec-landing' ); ?></strong> —
					<?php esc_html_e( 'Debes ingresar Site Key y Secret Key para activarlo.', 'sercotec-landing' ); ?>
				</p>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Rate limiting', 'sercotec-landing' ); ?></h2>
			<p><?php esc_html_e( 'Limita la cantidad de envíos permitidos desde la misma dirección IP.', 'sercotec-landing' ); ?></p>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="sercotec_contact_rate_limit_max"><?php esc_html_e( 'Máximo de envíos por IP', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="number" id="sercotec_contact_rate_limit_max" name="sercotec_contact_rate_limit_max" value="<?php echo esc_attr( (string) $rate_max ); ?>" min="1" max="20" class="small-text">
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="sercotec_contact_rate_limit_window"><?php esc_html_e( 'Ventana (minutos)', 'sercotec-landing' ); ?></label>
					</th>
					<td>
						<input type="number" id="sercotec_contact_rate_limit_window" name="sercotec_contact_rate_limit_window" value="<?php echo esc_attr( (string) $rate_window ); ?>" min="1" max="1440" class="small-text">
						<p class="description"><?php esc_html_e( 'Periodo en el que se aplica el límite de envíos por IP.', 'sercotec-landing' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button( __( 'Guardar cambios', 'sercotec-landing' ) ); ?>
		</form>
	</div>
	<?php
}
